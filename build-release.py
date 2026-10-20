#!/usr/bin/env python3
"""Build the installable Fonten ZIP using only explicitly listed runtime files."""

import re
import tempfile
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile


ROOT = Path(__file__).resolve().parent
# Add new runtime dependencies here when introducing them to the plugin.
RELEASE_FILES = (
    "fonten.php",
    "bootstrap.php",
    "includes/class-fonten-admin.php",
    "includes/class-fonten-cleanup.php",
    "includes/class-fonten-fonts.php",
    "views/settings-page.php",
    "assets/css/admin.css",
    "assets/js/settings.js",
    "readme.txt",
    "LICENSE",
)


def main():
    for relative in RELEASE_FILES:
        source = ROOT / relative
        if not source.is_file() or any(
            part.is_symlink() for part in (source, *source.parents)
        ):
            raise SystemExit(f"Missing runtime file or symbolic link: {relative}")

    match = re.search(
        r"^\s*\* Version:\s*([0-9]+(?:\.[0-9]+)*(?:[-+][a-zA-Z0-9.-]+)?)\s*$",
        (ROOT / "fonten.php").read_text(encoding="utf-8"),
        re.MULTILINE,
    )
    if not match:
        raise SystemExit("Cannot read the plugin version from fonten.php")

    output = ROOT / f"fonten-{match.group(1)}.zip"
    with tempfile.TemporaryDirectory(prefix="fonten-release-") as temporary:
        archive = Path(temporary) / output.name
        with ZipFile(archive, "w", compression=ZIP_DEFLATED) as package:
            for relative in RELEASE_FILES:
                source = ROOT / relative
                if source.suffix == ".php":
                    # Rename only the packaged copy; keep development branding in source.
                    content = source.read_text(encoding="utf-8").replace("FontenDev", "Fonten")
                    package.writestr(f"fonten/{relative}", content)
                else:
                    package.write(source, f"fonten/{relative}")
        with ZipFile(archive) as package:
            if package.testzip() is not None:
                raise SystemExit("ZIP integrity check failed")
        output.write_bytes(archive.read_bytes())
    print(output)


if __name__ == "__main__":
    main()
