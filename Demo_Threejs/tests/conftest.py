from __future__ import annotations

import io
import os
import signal
import subprocess
import time
from pathlib import Path

import pytest
from PIL import Image, ImageChops
from playwright.sync_api import sync_playwright

REPO_ROOT = Path(__file__).resolve().parent.parent.parent
HTML_PATH = "Demo_Threejs/systeq_v3_dev_v2.html"
PORT = 8765


def _free_port():
    """Kill any process listening on our port."""
    try:
        subprocess.run(
            ["netstat", "-ano", "|", "findstr", f":{PORT}"],
            shell=True, capture_output=True, timeout=3,
        )
        subprocess.run(
            ["taskkill", "/f", "/im", "python*", "/fi", f"PID ne {os.getpid()}"],
            shell=True, capture_output=True, timeout=3,
        )
    except Exception:
        pass


@pytest.fixture(scope="session")
def server_url():
    _free_port()
    proc = subprocess.Popen(
        ["python", "-m", "http.server", str(PORT), "--directory", str(REPO_ROOT)],
        stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL,
    )
    time.sleep(1)
    url = f"http://localhost:{PORT}"
    yield url
    proc.terminate()
    try:
        proc.wait(timeout=5)
    except subprocess.TimeoutExpired:
        proc.kill()
        proc.wait()


@pytest.fixture(scope="session")
def browser():
    with sync_playwright() as pw:
        yield pw.chromium.launch(
            channel="chrome",
            headless=True,
            args=["--no-sandbox", "--disable-gpu"],
        )


GOLDEN_DIR = Path(__file__).parent / "golden"
MAX_PIXEL_DIFF_PCT = 3.0


def screenshot_name(phase: str, variant: str = "") -> str:
    name = phase.lower().replace(" ", "_")
    if variant:
        name += f"_{variant}"
    return name


def _screenshot_to_pil(page) -> Image.Image:
    return Image.open(io.BytesIO(page.screenshot(full_page=False)))


def compare_or_update(page, phase: str, variant: str = "", update: bool = False):
    name = screenshot_name(phase, variant)
    golden_path = GOLDEN_DIR / f"{name}.png"
    actual = _screenshot_to_pil(page).convert("RGB")

    if update or not golden_path.exists():
        actual.save(golden_path)
        return 0.0

    golden = Image.open(golden_path).convert("RGB")
    diff = ImageChops.difference(actual, golden)
    total = actual.width * actual.height
    changed = sum(1 for p in diff.getdata() if p[0] > 5 or p[1] > 5 or p[2] > 5)
    diff_pct = changed / total * 100

    if diff_pct > MAX_PIXEL_DIFF_PCT:
        diff.save(golden_path.with_name(f"{name}_diff.png"))

    return diff_pct


@pytest.fixture
def page(server_url, browser):
    """Create a fresh page with the dev demo loaded."""
    context = browser.new_context(
        viewport={"width": 1280, "height": 720},
        device_scale_factor=1,
    )
    p = context.new_page()
    p.goto(f"{server_url}/{HTML_PATH}?dev", wait_until="load", timeout=30000)
    p.wait_for_selector("canvas", timeout=15000)
    p.wait_for_timeout(2000)
    yield p
    context.close()


@pytest.fixture
def update_golden(pytestconfig):
    return pytestconfig.getoption("--update-golden", default=False)


def pytest_addoption(parser):
    parser.addoption(
        "--update-golden", action="store_true", default=False,
        help="Update golden master screenshots instead of comparing",
    )
