<style>
    * {
        box-sizing: border-box;
    }

    body {
        min-height: 100vh;
        margin: 0;
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background:
            radial-gradient(circle at top left, rgba(45, 212, 191, 0.18), transparent 28%),
            linear-gradient(135deg, #0f172a, #132f39);
        color: #fff;
    }

    .wrap {
        width: min(920px, 100%);
        margin: 0 auto;
        padding: 42px 24px;
    }

    .card {
        padding: 34px;
        border-radius: 22px;
        background: rgba(30, 41, 59, 0.78);
        border: 1px solid rgba(148, 163, 184, 0.22);
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
    }

    .badge {
        display: inline-flex;
        margin-bottom: 18px;
        padding: 8px 14px;
        color: #86efac;
        border-radius: 999px;
        background: rgba(34, 197, 94, 0.14);
        font-weight: 800;
        line-height: 1.35;
    }

    h1 {
        margin: 0 0 14px;
        font-size: 42px;
        line-height: 1.15;
    }

    p,
    li {
        color: #cbd5e1;
        font-size: 18px;
        line-height: 1.7;
    }

    ol {
        margin: 24px 0;
        padding-left: 24px;
    }

    .actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 28px;
    }

    .note {
        margin-top: 20px;
        padding: 14px 16px;
        color: #bfdbfe;
        border-radius: 14px;
        background: rgba(14, 165, 233, 0.1);
        border: 1px solid rgba(56, 189, 248, 0.18);
    }

    .btn {
        display: inline-flex;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        padding: 12px 18px;
        border-radius: 14px;
        color: #fff;
        font-weight: 800;
        text-decoration: none;
        background: linear-gradient(135deg, #38bdf8, #2563eb);
        border: 1px solid rgba(148, 163, 184, 0.22);
        transition: 0.2s;
    }

    .btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.08);
    }

    .btn-secondary {
        background: rgba(15, 23, 42, 0.72);
    }

    @media (max-width: 640px) {
        .wrap {
            padding: 22px 12px;
        }

        .card {
            padding: 24px 18px;
            border-radius: 18px;
        }

        .badge {
            border-radius: 16px;
        }

        h1 {
            font-size: 30px;
        }

        p,
        li {
            font-size: 16px;
        }

        ol {
            padding-left: 20px;
        }

        .btn {
            width: 100%;
            min-height: 52px;
            text-align: center;
        }

        .actions {
            display: grid;
        }
    }
</style>
