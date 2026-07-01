#!/usr/bin/env python3
"""Seed portfolio.db with skills, 1000+ projects, and 500+ blog posts."""
import sqlite3
import random
import hashlib
from datetime import datetime, timedelta
from pathlib import Path

DB_PATH = Path(__file__).resolve().parent / "portfolio.db"

ICON_SLUGS = {
    "OpenAI GPT": "openai", "OpenAI": "openai", "ChatGPT": "openai",
    "Claude": "anthropic", "Anthropic": "anthropic",
    "Gemini": "googlegemini", "Google Gemini": "googlegemini",
    "Llama": "meta", "Meta Llama": "meta",
    "Qwen": "alibabacloud", "DeepSeek": "deepseek",
    "Mistral": "mistral", "Mistral AI": "mistral",
    "Gemma": "google", "Phi": "microsoft", "Cohere": "cohere",
    "Ollama": "ollama", "LangChain": "langchain", "LangGraph": "langchain",
    "LlamaIndex": "llamaindex", "CrewAI": "crewai", "AutoGen": "microsoft",
    "Pinecone": "pinecone", "Weaviate": "weaviate", "Milvus": "milvus",
    "Qdrant": "qdrant", "Chroma": "chroma", "Elasticsearch": "elasticsearch",
    "OpenSearch": "opensearch", "Redis Vector": "redis",
    "Figma": "figma", "FigJam": "figma", "Adobe XD": "adobe",
    "Sketch": "sketch", "Framer": "framer", "Penpot": "penpot",
    "Miro": "miro", "Zeplin": "zeplin", "InVision": "invision",
    "Adobe Photoshop": "adobephotoshop", "Adobe Illustrator": "adobeillustrator",
    "Adobe After Effects": "adobeaftereffects", "Canva": "canva",
    "HTML5": "html5", "CSS3": "css3", "Sass": "sass",
    "Bootstrap": "bootstrap", "Tailwind CSS": "tailwindcss", "Tailwind": "tailwindcss",
    "JavaScript (ES6+)": "javascript", "JavaScript": "javascript",
    "TypeScript": "typescript", "React": "react", "Next.js": "nextdotjs",
    "Angular": "angular", "Vite": "vite", "React Native": "react",
    "Flutter": "flutter", "Vue.js": "vuedotjs", "Svelte": "svelte",
    "Git": "git", "GitHub": "github", "GitLab": "gitlab",
    "Docker (Working Knowledge)": "docker", "Docker": "docker",
    "AWS Fundamentals": "amazonwebservices", "AWS": "amazonwebservices",
    "AWS Amplify": "awsamplify", "Microsoft Azure Fundamentals": "microsoftazure",
    "Google Cloud Fundamentals": "googlecloud", "Firebase": "firebase",
    "Vercel": "vercel", "Netlify": "netlify", "Cloudflare": "cloudflare",
    "GitHub Actions": "githubactions", "Jira": "jira", "Slack": "slack",
    "Notion": "notion", "Confluence": "confluence", "Trello": "trello",
    "Microsoft Teams": "microsoftteams", "Hotjar": "hotjar",
    "Mixpanel": "mixpanel", "Google Analytics 4": "googleanalytics",
    "Typeform": "typeform", "Postman": "postman", "FastAPI": "fastapi",
    "PostgreSQL": "postgresql", "MongoDB": "mongodb", "Redis": "redis",
    "Supabase": "supabase", "Kubernetes": "kubernetes",
    "VS Code": "visualstudiocode", "Cursor AI": "cursor", "Cursor": "cursor",
    "GitHub Copilot": "githubcopilot", "Perplexity AI": "perplexity",
    "Microsoft Copilot": "microsoft", "Replit AI": "replit",
    "Uizard": "uizard", "n8n": "n8n", "Flowise": "flowise",
    "Dify": "dify", "UIPath Studio": "uipath", "Selenium UX Testing": "selenium",
    "Playwright": "playwright", "LangSmith": "langchain", "MLflow": "mlflow",
    "Prometheus": "prometheus", "Grafana": "grafana", "Sentry": "sentry",
    "Terraform": "terraform", "Nginx": "nginx", "Express.js": "express",
    "NestJS": "nestjs", "Python": "python", "Node.js": "nodedotjs",
    "Material UI": "mui", "shadcn/ui": "shadcnui",
}

SKILLS_BY_CATEGORY = {
    "AI-Driven Design": [
        "Figma AI Plugins", "Uizard", "Magician", "Zeplin", "Anima", "CopyDoc",
    ],
    "AI-Assisted UX & Design": [
        "AI Product Design", "Conversational UX", "AI Chatbot UX", "AI Agent UX",
        "Prompt Engineering", "AI Workflow Design", "LLM User Experience",
        "Human-AI Interaction", "AI Design Patterns", "AI Feature Discovery",
        "AI Personalization", "AI-powered Prototyping",
    ],
    "Generative AI & LLM Ecosystem": [
        "OpenAI GPT", "Claude", "Gemini", "Llama", "Qwen", "DeepSeek", "Mistral",
        "Gemma", "Phi", "Cohere", "Ollama", "LM Studio", "Open WebUI",
        "LangChain (Working Knowledge)", "LangGraph", "LlamaIndex (Working Knowledge)",
        "openclaw", "CrewAI", "AutoGen", "AI Agents", "MCP (Model Context Protocol)",
        "Retrieval-Augmented Generation (RAG)", "Function Calling", "Tool Calling",
        "OpenHands", "OpenDevin", "OpenClaw", "Continue", "Cline", "Roo Code",
        "Flowise", "Dify", "AnythingLLM", "LibreChat", "Big-AGI",
    ],
    "Vector Databases & Search": [
        "Pinecone", "Weaviate", "Milvus", "Qdrant", "Chroma", "FAISS", "pgvector",
        "Redis Vector", "Elasticsearch", "OpenSearch", "LanceDB",
    ],
    "UX Strategy & Product Design": [
        "User Experience (UX) Strategy", "User Interface (UI) Design", "Product Design",
        "Design Thinking", "Human-Centered Design (HCD)", "User-Centered Design (UCD)",
        "Information Architecture (IA)", "Interaction Design (IxD)", "Visual Design",
        "Responsive Web Design", "Mobile App Design (iOS & Android)", "Cross-Platform Design",
        "Accessibility (WCAG 2.2)", "Inclusive Design", "Design Systems", "Atomic Design",
        "Design Tokens", "Component Libraries", "Enterprise Design Systems", "Design Governance",
    ],
    "UX Research": [
        "User Research", "Stakeholder Interviews", "Persona Development",
        "Customer Journey Mapping", "User Flows", "Task Analysis", "Heuristic Evaluation",
        "Competitive Analysis", "Usability Testing", "A/B Testing", "Card Sorting",
        "Tree Testing", "Heatmap Analysis", "Survey Design", "Analytics-Driven UX",
    ],
    "Wireframing & Prototyping": [
        "Low-Fidelity Wireframes", "High-Fidelity UI", "Interactive Prototypes",
        "Clickable Prototypes", "Design Specifications", "Motion Design",
        "Micro-interactions", "Animation Design", "Responsive Prototyping",
    ],
    "Design & Collaboration Tools": [
        "Figma", "FigJam", "Adobe XD", "Sketch", "Framer", "Penpot", "UXPin",
        "Miro", "Zeplin", "InVision", "Adobe Photoshop", "Adobe Illustrator",
        "Adobe After Effects", "Adobe Premiere Pro", "Canva", "Microsoft Visio",
    ],
    "UX Research & Analytics": [
        "Maze", "UserTesting", "PlaybookUX", "Lookback", "Hotjar", "Mixpanel",
        "Google Analytics 4", "Amplitude", "Heap Analytics", "Smartlook",
        "Microsoft Clarity", "Dovetail", "UserZoom", "Optimal Workshop", "Typeform",
    ],
    "Accessibility & Inclusive Design": [
        "WCAG 2.2", "WAVE", "axe DevTools", "Stark", "Accessibility Audits",
        "Inclusive Design", "ADA Compliance", "Accessibility Testing",
    ],
    "Frontend Technologies": [
        "HTML5", "CSS3", "Sass", "Bootstrap", "Tailwind CSS", "JavaScript (ES6+)",
        "TypeScript", "React", "Next.js", "Angular", "Vite", "React Native",
        "Flutter", "Responsive Layouts",
    ],
    "AI Design & Productivity Tools": [
        "ChatGPT", "Claude", "Gemini", "Perplexity AI", "Microsoft Copilot",
        "GitHub Copilot", "Cursor AI", "Windsurf", "Lovable", "Bolt.new",
        "Replit AI", "Galileo AI", "Uizard", "Attention Insight", "Magician (Figma Plugin)",
    ],
    "Cloud & Dev Collaboration": [
        "Git", "GitHub", "GitLab", "Docker (Working Knowledge)", "AWS Fundamentals",
        "Microsoft Azure Fundamentals", "Google Cloud Fundamentals", "Vercel", "Netlify",
    ],
    "Cloud & DevOps": [
        "Azure UX Services", "AWS Amplify", "Firebase", "GitHub Actions", "CI/CD",
    ],
    "Project Management & Collaboration": [
        "Agile", "Scrum", "Kanban", "Jira", "Azure DevOps", "Trello", "Confluence",
        "Notion", "Slack", "Microsoft Teams",
    ],
    "Delivery Leadership": [
        "Agile/Scrum", "Jira", "SOWs", "Project Roadmaps", "P&L", "Stakeholder Mgmt",
    ],
    "Client Management": [
        "Client Workshops", "Journey Mapping", "User Story Backlog Grooming", "Demos",
    ],
    "Leadership & Design Management": [
        "UX Team Leadership", "Design Mentoring", "Design Reviews", "Stakeholder Management",
        "Client Presentations", "Product Strategy", "Cross-functional Collaboration",
        "Workshop Facilitation", "Sprint Planning", "UX Roadmap Planning",
        "Design Operations (DesignOps)", "Design Quality Assurance",
    ],
    "Robotic Automation": [
        "UIPath Studio", "Robocorp", "Selenium UX Testing",
    ],
    "Inference & MLOps": [
        "vLLM", "llama.cpp", "TensorRT-LLM", "SGLang", "LM Studio", "Langfuse",
        "Ragas", "Promptfoo", "Guardrails AI", "Haystack", "GraphRAG", "LightRAG",
    ],
}

PROJECT_CATEGORIES = [
    "UX Strategy", "Product Design", "AI Agent UX", "Design Systems",
    "Mobile App Design", "Enterprise UX", "Conversational UX", "RAG Product Design",
    "Accessibility", "HCI Research", "Fintech UX", "Healthtech UX", "SaaS Dashboard",
    "E-commerce UX", "Generative AI Interface",
]

INDUSTRIES = [
    "healthcare", "banking", "insurance", "retail", "logistics", "education",
    "government", "telecom", "media", "travel", "real estate", "agriculture",
    "manufacturing", "energy", "legal tech", "HR tech", "cybersecurity",
]

PROJECT_TITLES = [
    "Redesigning {industry} onboarding so users stop abandoning at step two",
    "A design system that finally made {industry} teams ship consistent UI",
    "Agentic assistant UX for {industry} support — fewer handoffs, faster resolution",
    "Mobile checkout refresh for a {industry} marketplace",
    "RAG-powered search experience in a {industry} knowledge portal",
    "WCAG 2.2 audit and remediation for a {industry} public platform",
    "Conversation flows for an AI copilot in {industry} operations",
    "Dashboard IA for {industry} analysts who live in spreadsheets",
    "Prototype sprint: validating {feature} with real {industry} users",
    "Enterprise design tokens for a multi-brand {industry} group",
]

BLOG_TITLES = [
    "What I learned designing {topic} for skeptical enterprise buyers",
    "Why {topic} breaks when you copy consumer app patterns blindly",
    "A practical checklist for {topic} before you ship v1",
    "Notes from usability tests: {topic} in the wild",
    "How {topic} changes when LLMs enter the workflow",
    "Designing trust into {topic} — patterns that actually work",
    "From workshop to wireframe: {topic} in two weeks",
    "The {topic} mistakes I keep seeing on Behance portfolios",
]

BLOG_TOPICS = [
    "AI agent handoffs", "design tokens at scale", "chatbot empty states",
    "RAG citation UX", "accessibility in dark mode", "journey mapping workshops",
    "prompt-driven prototyping", "enterprise table UX", "mobile form design",
    "stakeholder demo storytelling", "human-in-the-loop flows", "vector search UX",
    "onboarding for B2B SaaS", "error recovery in conversational UI",
    "design ops for distributed teams", "WCAG focus indicators", "persona skepticism",
]

BLOG_OPENINGS = [
    "Last month I ran a workshop with a team that had been stuck on {topic} for weeks.",
    "I've shipped {topic} across fintech, health, and SaaS — the context changes, but the friction patterns repeat.",
    "If you've browsed ThemeForest lately, you've seen glossy {topic} mockups. Real products need more than hero shots.",
    "A client asked me to review their Behance case study on {topic}. The visuals were strong; the decision trail wasn't.",
    "Here's the honest version of how I approach {topic} when timelines are tight and politics are real.",
]

BLOG_MIDDLES = [
    "We started with five user interviews, not a 40-page deck. That surfaced the actual decision points.",
    "The breakthrough came when we mapped where humans still need override — not everything belongs in the model.",
    "I paired low-fi flows with a clickable prototype in Figma. Stakeholders stopped debating colors and started debating tasks.",
    "We measured task success with Maze before polishing pixels. It saved two sprint cycles.",
    "The design system team joined early. Tokens for spacing and type prevented the usual 'every screen is a snowflake' drift.",
]

BLOG_CLOSINGS = [
    "If you're tackling something similar, book a call — I share frameworks, not generic slide decks.",
    "Happy to walk through the Figma file structure if you're building a comparable flow.",
    "This isn't theory from a template; it's what held up in production reviews.",
    "Reach out if you want a second pair of eyes before your next stakeholder demo.",
]

PROJECT_IMAGES = [f"assets/img/projects/project-{(i % 12) + 1}.png" for i in range(12)]
PROJECT_IMAGES += [
    "assets/img/projects/alphastreet.png",
    "assets/img/projects/project11.png",
    "assets/img/projects/ai-agent-1.png",
    "assets/img/projects/gen-ux-1.png",
]

BLOG_IMAGES = [f"assets/img/blog/blog-{(i % 8) + 1}.jpg" for i in range(8)]
BLOG_IMAGES += [f"assets/img/projects/project-{(i % 6) + 1}.png" for i in range(6)]


def icon_url(name: str) -> str:
    if name in ICON_SLUGS:
        return f"https://cdn.simpleicons.org/{ICON_SLUGS[name]}"
    base = name.split("(")[0].strip().lower()
    for key, slug in ICON_SLUGS.items():
        if key.lower() in base or base in key.lower():
            return f"https://cdn.simpleicons.org/{slug}"
    slug = "".join(c for c in base if c.isalnum())
    if slug:
        return f"https://cdn.simpleicons.org/{slug}/4770FF"
    return "assets/img/icons/ui-ux.svg"


def init_db(conn: sqlite3.Connection) -> None:
    conn.executescript("""
        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            category TEXT,
            image TEXT,
            link TEXT,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS blogs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            image TEXT,
            excerpt TEXT,
            content TEXT,
            category TEXT,
            date TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS skills (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            icon_url TEXT,
            category TEXT
        );
        CREATE TABLE IF NOT EXISTS settings (
            key TEXT PRIMARY KEY,
            value TEXT
        );
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE,
            password TEXT
        );
        CREATE INDEX IF NOT EXISTS idx_projects_category ON projects(category);
        CREATE INDEX IF NOT EXISTS idx_blogs_category ON blogs(category);
    """)


def seed_skills(conn: sqlite3.Connection) -> int:
    conn.execute("DELETE FROM skills")
    seen = set()
    count = 0
    for category, names in SKILLS_BY_CATEGORY.items():
        for name in names:
            if name in seen:
                continue
            seen.add(name)
            conn.execute(
                "INSERT INTO skills (name, category, icon_url) VALUES (?, ?, ?)",
                (name, category, icon_url(name)),
            )
            count += 1
    return count


def seed_projects(conn: sqlite3.Connection, total: int = 1000) -> int:
    conn.execute("DELETE FROM projects")
    features = [
        "self-serve onboarding", "claims intake", "vendor portal",
        "patient scheduling", "loyalty program", "inventory alerts",
    ]
    rng = random.Random(42)
    for i in range(total):
        industry = rng.choice(INDUSTRIES)
        category = rng.choice(PROJECT_CATEGORIES)
        title_tpl = rng.choice(PROJECT_TITLES)
        title = title_tpl.format(industry=industry, feature=rng.choice(features))
        desc = (
            f"Led end-to-end UX for a {industry} initiative focused on {category.lower()}. "
            f"Work included discovery interviews, journey maps, high-fidelity UI, and dev handoff. "
            f"Deliverables aligned with WCAG 2.2 and an existing design system where possible."
        )
        link = f"portfolio-details?id={i + 1}"
        img = PROJECT_IMAGES[i % len(PROJECT_IMAGES)]
        days_ago = rng.randint(1, 900)
        created = (datetime.now() - timedelta(days=days_ago)).strftime("%Y-%m-%d %H:%M:%S")
        conn.execute(
            "INSERT INTO projects (title, category, image, link, description, created_at) VALUES (?,?,?,?,?,?)",
            (title, category, img, link, desc, created),
        )
    return total


def seed_blogs(conn: sqlite3.Connection, total: int = 500) -> int:
    conn.execute("DELETE FROM blogs")
    rng = random.Random(99)
    for i in range(total):
        topic = rng.choice(BLOG_TOPICS)
        category = rng.choice(PROJECT_CATEGORIES)
        title = rng.choice(BLOG_TITLES).format(topic=topic)
        opening = rng.choice(BLOG_OPENINGS).format(topic=topic)
        middle = rng.choice(BLOG_MIDDLES)
        closing = rng.choice(BLOG_CLOSINGS)
        content = f"<p>{opening}</p><p>{middle}</p><p>{closing}</p>"
        excerpt = opening[:180] + ("…" if len(opening) > 180 else "")
        days_ago = rng.randint(1, 600)
        date = (datetime.now() - timedelta(days=days_ago)).strftime("%d %b %Y")
        img = BLOG_IMAGES[i % len(BLOG_IMAGES)]
        conn.execute(
            "INSERT INTO blogs (title, image, excerpt, content, category, date) VALUES (?,?,?,?,?,?)",
            (title, img, excerpt, content, category, date),
        )
    return total


def seed_settings(conn: sqlite3.Connection) -> None:
    defaults = {
        "ga_measurement_id": "G-XXXXXXXXXX",
        "google_meet_link": "https://meet.google.com/new",
        "cal_username": "praveenkumar-kanneganti",
        "site_url": "https://www.pranuuxui.com",
        "contact_email": "praveenkumar.kanneganti@gmail.com",
    }
    for key, value in defaults.items():
        conn.execute(
            "INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)",
            (key, value),
        )


def main() -> None:
    DB_PATH.parent.mkdir(parents=True, exist_ok=True)
    conn = sqlite3.connect(DB_PATH)
    try:
        init_db(conn)
        skills = seed_skills(conn)
        projects = seed_projects(conn, 1000)
        blogs = seed_blogs(conn, 500)
        seed_settings(conn)
        conn.commit()
        print(f"Seeded {skills} skills, {projects} projects, {blogs} blogs -> {DB_PATH}")
    finally:
        conn.close()


if __name__ == "__main__":
    main()
