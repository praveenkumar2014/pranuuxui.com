#!/usr/bin/env python3
"""Generate placeholder raster assets required by the portfolio site."""
from pathlib import Path

try:
    from PIL import Image, ImageDraw, ImageFont
except ImportError:
    import subprocess
    import sys
    subprocess.check_call([sys.executable, "-m", "pip", "install", "pillow", "-q"])
    from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parent.parent
DIRS = [
    ROOT / "assets" / "img" / "images",
    ROOT / "assets" / "img" / "bg",
    ROOT / "assets" / "img" / "projects",
    ROOT / "assets" / "img" / "blog",
    ROOT / "modern-portfolio" / "assets" / "img" / "images",
    ROOT / "modern-portfolio" / "assets" / "img" / "bg",
    ROOT / "modern-portfolio" / "assets" / "img" / "projects",
    ROOT / "modern-portfolio" / "assets" / "img" / "blog",
]

COLORS = [
    (71, 112, 255),
    (99, 102, 241),
    (16, 185, 129),
    (245, 158, 11),
    (236, 72, 153),
    (14, 165, 233),
]


def gradient(size, c1, c2):
    w, h = size
    img = Image.new("RGB", size, c1)
    draw = ImageDraw.Draw(img)
    for y in range(h):
        t = y / max(h - 1, 1)
        r = int(c1[0] * (1 - t) + c2[0] * t)
        g = int(c1[1] * (1 - t) + c2[1] * t)
        b = int(c1[2] * (1 - t) + c2[2] * t)
        draw.line([(0, y), (w, y)], fill=(r, g, b))
    return img


def save_png(path: Path, img: Image.Image) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    img.save(path, "PNG")
    print(f"  created {path.relative_to(ROOT)}")


def profile_image() -> Image.Image:
    img = gradient((512, 512), (71, 112, 255), (30, 30, 60))
    draw = ImageDraw.Draw(img)
    draw.ellipse((156, 80, 356, 320), fill=(255, 255, 255, 200))
    draw.rounded_rectangle((120, 340, 392, 480), radius=40, fill=(255, 255, 255, 200))
    return img


def banner_shape() -> Image.Image:
    img = Image.new("RGBA", (400, 400), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    draw.ellipse((20, 20, 380, 380), fill=(71, 112, 255, 80))
    draw.ellipse((80, 80, 320, 320), fill=(99, 102, 241, 60))
    return img


def object_3d(idx: int) -> Image.Image:
    c = COLORS[idx % len(COLORS)]
    img = Image.new("RGBA", (300, 300), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    draw.polygon([(150, 20), (280, 120), (150, 220), (20, 120)], fill=(*c, 200))
    draw.polygon([(150, 80), (280, 180), (150, 280), (20, 180)], fill=(*[max(0, x - 40) for x in c], 180))
    return img


def project_image(name: str, idx: int) -> Image.Image:
    c1 = COLORS[idx % len(COLORS)]
    c2 = COLORS[(idx + 2) % len(COLORS)]
    img = gradient((800, 600), c1, c2)
    draw = ImageDraw.Draw(img)
    draw.rounded_rectangle((40, 40, 760, 560), radius=24, outline=(255, 255, 255), width=3)
    label = name.replace("-", " ").replace(".png", "").title()[:24]
    draw.text((60, 500), label, fill=(255, 255, 255))
    return img


def blog_image(idx: int) -> Image.Image:
    return project_image(f"blog-{idx}", idx + 3)


def minimal_pdf(path: Path) -> None:
    """Write a minimal valid PDF resume placeholder."""
    content = b"""%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>endobj
4 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj
5 0 obj<</Length 120>>stream
BT /F1 24 Tf 72 720 Td (Praveen Kumar K) Tj 0 -36 Td /F1 14 Tf (Senior UX/UI Architect and AI Design Strategist) Tj ET
endstream endobj
xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000266 00000 n 
0000000345 00000 n 
trailer<</Size 6/Root 1 0 R>>
startxref
520
%%EOF"""
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_bytes(content)
    print(f"  created {path.relative_to(ROOT)}")


def main() -> None:
    for d in DIRS:
        d.mkdir(parents=True, exist_ok=True)

    targets = [
        ROOT / "assets",
        ROOT / "modern-portfolio" / "assets",
    ]

    project_names = [f"project-{i}.png" for i in range(1, 13)]
    project_names += [
        "alphastreet.png",
        "project11.png",
        "ai-agent-1.png",
        "gen-ux-1.png",
    ]

    for base in targets:
        save_png(base / "img" / "images" / "profile.png", profile_image())
        save_png(base / "img" / "bg" / "banner-shape-1.png", banner_shape())
        save_png(base / "img" / "bg" / "object-3d-1.png", object_3d(0))
        save_png(base / "img" / "bg" / "object-3d-2.png", object_3d(1))
        for i, name in enumerate(project_names):
            save_png(base / "img" / "projects" / name, project_image(name, i))
        for i in range(1, 9):
            save_png(base / "img" / "blog" / f"blog-{i}.jpg", blog_image(i))
        minimal_pdf(base / "Praveen_Kumar_K_Resume.pdf")

    print("Asset setup complete.")


if __name__ == "__main__":
    main()
