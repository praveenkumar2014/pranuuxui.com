#!/usr/bin/env python3
"""Add default admin user to portfolio.db for CMS login."""
import sqlite3
from pathlib import Path

try:
    import bcrypt
except ImportError:
    import subprocess
    import sys
    subprocess.check_call([sys.executable, "-m", "pip", "install", "bcrypt", "-q"])
    import bcrypt

ROOT = Path(__file__).resolve().parent.parent
DB_PATHS = [
    ROOT / "db" / "portfolio.db",
    ROOT / "modern-portfolio" / "db" / "portfolio.db",
]

USERNAME = "admin"
PASSWORD = "Praveen@2026"
# PHP password_hash compatible bcrypt ($2y$ prefix)
password_hash = bcrypt.hashpw(PASSWORD.encode(), bcrypt.gensalt(rounds=10)).decode()
password_hash = password_hash.replace("$2b$", "$2y$")


def seed(path: Path) -> None:
    if not path.exists():
        print(f"skip missing {path}")
        return
    conn = sqlite3.connect(path)
    conn.execute(
        "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT UNIQUE, password TEXT)"
    )
    conn.execute("DELETE FROM users WHERE username = ?", (USERNAME,))
    conn.execute("INSERT INTO users (username, password) VALUES (?, ?)", (USERNAME, password_hash))
    conn.commit()
    conn.close()
    print(f"admin user seeded -> {path}")


if __name__ == "__main__":
    for db in DB_PATHS:
        seed(db)
