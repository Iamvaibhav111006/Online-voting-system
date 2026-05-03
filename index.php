<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#f7f1e8;
            --ink:#1d1a16;
            --muted:#62584d;
            --card:#fffaf3;
            --line:#e6d9ca;
            --accent:#b45309;
            --accent-soft:#fde7d0;
            --accent-2:#0f766e;
            --shadow:0 28px 60px rgba(74, 54, 24, 0.14);
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"DM Sans", sans-serif;
            color:var(--ink);
            background:
                radial-gradient(circle at top right, rgba(180,83,9,0.12), transparent 25%),
                radial-gradient(circle at left 20%, rgba(15,118,110,0.10), transparent 22%),
                linear-gradient(180deg, #f8f2ea 0%, #f3eadf 100%);
        }

        .page{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:28px;
        }

        .shell{
            width:min(1200px, 100%);
            display:grid;
            grid-template-columns:minmax(0, 1.3fr) 420px;
            gap:28px;
            align-items:stretch;
        }

        .hero{
            background:linear-gradient(145deg, rgba(255,250,243,0.92), rgba(255,246,236,0.96));
            border:1px solid var(--line);
            border-radius:32px;
            padding:48px;
            box-shadow:var(--shadow);
            position:relative;
            overflow:hidden;
        }

        .hero::before{
            content:"";
            position:absolute;
            inset:auto -60px -60px auto;
            width:220px;
            height:220px;
            border-radius:50%;
            background:rgba(180,83,9,0.10);
        }

        .eyebrow{
            display:inline-flex;
            padding:10px 16px;
            border-radius:999px;
            background:var(--accent-soft);
            color:var(--accent);
            font-size:12px;
            font-weight:700;
            letter-spacing:0.08em;
            text-transform:uppercase;
        }

        h1{
            margin:24px 0 18px;
            max-width:720px;
            font-family:"Space Grotesk", sans-serif;
            font-size:clamp(42px, 6vw, 74px);
            line-height:0.95;
        }

        .subtitle{
            max-width:620px;
            margin:0;
            color:var(--muted);
            font-size:19px;
            line-height:1.8;
        }

        .highlights{
            display:grid;
            grid-template-columns:repeat(3, minmax(0, 1fr));
            gap:16px;
            margin-top:34px;
        }

        .highlight{
            padding:18px;
            border-radius:22px;
            background:#fff;
            border:1px solid var(--line);
        }

        .highlight strong{
            display:block;
            margin-bottom:6px;
            font-family:"Space Grotesk", sans-serif;
            font-size:24px;
        }

        .highlight span{
            color:var(--muted);
            font-size:14px;
            line-height:1.6;
        }

        .portal{
            background:#1d1a16;
            color:#fff9f1;
            border-radius:32px;
            padding:30px;
            box-shadow:var(--shadow);
            display:flex;
            flex-direction:column;
            justify-content:space-between;
        }

        .portal h2{
            margin:0 0 10px;
            font-family:"Space Grotesk", sans-serif;
            font-size:34px;
        }

        .portal p{
            margin:0;
            color:rgba(255,249,241,0.72);
            line-height:1.8;
        }

        .portal-list{
            display:grid;
            gap:16px;
            margin:26px 0;
        }

        .portal-link{
            display:block;
            text-decoration:none;
            color:inherit;
            padding:20px;
            border-radius:22px;
            background:rgba(255,255,255,0.06);
            border:1px solid rgba(255,255,255,0.10);
            transition:transform 0.2s ease, background 0.2s ease;
        }

        .portal-link:hover{
            transform:translateY(-2px);
            background:rgba(255,255,255,0.10);
        }

        .portal-title{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            margin-bottom:8px;
            font-family:"Space Grotesk", sans-serif;
            font-size:22px;
            font-weight:700;
        }

        .tag{
            display:inline-flex;
            padding:8px 11px;
            border-radius:999px;
            font-size:11px;
            font-weight:700;
            letter-spacing:0.08em;
            text-transform:uppercase;
        }

        .tag.admin{
            background:rgba(248,113,113,0.14);
            color:#fca5a5;
        }

        .tag.voter{
            background:rgba(94,234,212,0.14);
            color:#99f6e4;
        }

        .credit{
            color:rgba(255,249,241,0.56);
            font-size:13px;
        }

        @media (max-width: 980px){
            .shell{ grid-template-columns:1fr; }
        }

        @media (max-width: 720px){
            .page{ padding:16px; }
            .hero, .portal{ padding:24px; }
            .highlights{ grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="shell">
            <div class="hero">
                <span class="eyebrow">College Election Hub</span>
                <h1>Voting that feels clear, calm, and trustworthy.</h1>
                <p class="subtitle">
                    Bring your campus election online with a cleaner experience for students and administrators,
                    from sign-in and verification through ballot submission and result handling.
                </p>

                <div class="highlights">
                    <div class="highlight">
                        <strong>Secure</strong>
                        <span>Role-based access and controlled election steps keep participation structured.</span>
                    </div>
                    <div class="highlight">
                        <strong>Simple</strong>
                        <span>Direct entry points remove confusion for both administrators and voters.</span>
                    </div>
                    <div class="highlight">
                        <strong>Modern</strong>
                        <span>A more polished digital front door for your college election system.</span>
                    </div>
                </div>
            </div>

            <aside class="portal">
                <div>
                    <h2>Select Portal</h2>
                    <p>Choose your role to continue into the correct election workspace.</p>

                    <div class="portal-list">
                        <a href="admin_login.php" class="portal-link">
                            <div class="portal-title">
                                <span>Admin Login</span>
                                <span class="tag admin">Control</span>
                            </div>
                            <p>Manage candidates, election windows, reset tools, and results.</p>
                        </a>

                        <a href="login.php" class="portal-link">
                            <div class="portal-title">
                                <span>Voter Login</span>
                                <span class="tag voter">Ballot</span>
                            </div>
                            <p>Sign in, verify identity, and cast your vote during the live session.</p>
                        </a>
                    </div>
                </div>

                <div class="credit">Online Voting System</div>
            </aside>
        </section>
    </main>
</body>
</html>
