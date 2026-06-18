from __future__ import annotations

from pathlib import Path

import pytest

from conftest import GOLDEN_DIR, compare_or_update, MAX_PIXEL_DIFF_PCT


@pytest.fixture(autouse=True)
def ensure_golden_dir():
    GOLDEN_DIR.mkdir(parents=True, exist_ok=True)


def _phase(page) -> str:
    return page.evaluate("window.__devPhase()")


def _advance_to(page, target_sim_time: float):
    """Freeze, jump simTime directly, wait for render to settle."""
    page.evaluate("window.__devPause.set(true)")
    page.evaluate(f"window.__devSetSimTime({target_sim_time})")
    page.evaluate("window.__devResetAnimPhases()")
    page.wait_for_timeout(500)


def _screenshot_phase(page, phase_label: str, update: bool = False):
    assert _phase(page) == phase_label, (
        f"Expected {phase_label}, got {_phase(page)}"
    )
    diff_pct = compare_or_update(page, phase_label, update=update)
    assert diff_pct < MAX_PIXEL_DIFF_PCT, (
        f"{phase_label} golden master mismatch: {diff_pct:.1f}% pixels differ"
    )


class TestGoldenMasterPhases:

    def test_chaos_phase(self, page, update_golden):
        _advance_to(page, 0.5)
        _screenshot_phase(page, "CHAOS", update_golden)

    def test_activation_phase(self, page, update_golden):
        _advance_to(page, 8.5)
        _screenshot_phase(page, "ACTIVATION", update_golden)

    def test_order_phase(self, page, update_golden):
        _advance_to(page, 16.0)
        _screenshot_phase(page, "ORDER", update_golden)

    def test_idle_phase(self, page, update_golden):
        _advance_to(page, 25.0)
        _screenshot_phase(page, "IDLE", update_golden)


class TestSanityChecks:

    def test_canvas_exists(self, page):
        canvas = page.query_selector("canvas")
        assert canvas is not None
        box = canvas.bounding_box()
        assert box is not None
        assert box["width"] > 100
        assert box["height"] > 100

    def test_hud_elements_exist(self, page):
        for sid in ["#hud-container", "#cluster-map", "#hud-status",
                     "#live-clock", "#crt", ".top-bar", ".right-sidebar"]:
            assert page.query_selector(sid) is not None, f"Missing: {sid}"

    def test_dev_gui_loads(self, page):
        gui = page.query_selector(".lil-gui, .dg.main, .dg")
        assert gui is not None, "lil-gui not found"

    def test_phase_transitions_occur(self, page):
        assert _phase(page) == "CHAOS"
        _advance_to(page, 12.0)
        assert _phase(page) in ("ACTIVATION", "ORDER", "IDLE")


class TestOperatorGeometry:

    def test_operator_body_parts(self, page):
        parts = page.evaluate("Object.keys(window.__devParts())")
        vals = page.evaluate(
            "Object.values(window.__devParts()).map(v => v !== null && v !== undefined)"
        )
        for name, ok in zip(parts, vals):
            assert ok, f"Missing or null operator part: {name}"


if __name__ == "__main__":
    pytest.main([__file__, "-v"])
