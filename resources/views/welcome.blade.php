<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light dark; --fg:#1a1d21; --muted:#5b6470; --bg:#fbfbfc; --card:#fff; --line:#e3e6ea; --accent:#2f6f4f; }
        @media (prefers-color-scheme: dark) {
            :root { --fg:#e9ecef; --muted:#9aa4b0; --bg:#14171a; --card:#1b1f23; --line:#2b3138; --accent:#7fc9a2; }
        }
        * { box-sizing: border-box; }
        body { margin:0; padding:3rem 1.25rem; background:var(--bg); color:var(--fg);
               font:15px/1.6 ui-sans-serif, -apple-system, "Segoe UI", Roboto, sans-serif; }
        main { max-width: 46rem; margin: 0 auto; }
        h1 { font-size:1.5rem; margin:0 0 .35rem; letter-spacing:-.01em; }
        p.lede { margin:0 0 2rem; color:var(--muted); }
        ul { list-style:none; margin:0; padding:0; border:1px solid var(--line);
             border-radius:10px; overflow:hidden; background:var(--card); }
        li { display:flex; gap:.85rem; align-items:baseline; padding:.7rem 1rem; border-top:1px solid var(--line); }
        li:first-child { border-top:0; }
        code { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size:.85em; }
        .verb { flex:0 0 4.2rem; font-weight:600; color:var(--accent); font-size:.78rem;
                letter-spacing:.04em; text-transform:uppercase; }
        .path { flex:1 1 auto; }
        .desc { flex:0 0 100%; color:var(--muted); font-size:.85rem; padding-left:5.05rem; margin-top:-.3rem; }
        footer { margin-top:1.5rem; color:var(--muted); font-size:.85rem; }
    </style>
</head>
<body>
<main>
    <h1>{{ config('app.name') }}</h1>
    <p class="lede">A JSON API for teams, their members, and the projects those members work on.</p>

    <ul>
        @foreach ([
            ['GET',    '/api/teams',                            'List teams (paginated)'],
            ['POST',   '/api/teams',                            'Create a team'],
            ['GET',    '/api/teams/{team}',                     'Show one team'],
            ['GET',    '/api/teams/{team}/members',             'Members of a team'],
            ['GET',    '/api/members',                          'List members (paginated)'],
            ['POST',   '/api/members',                          'Create a member'],
            ['PATCH',  '/api/members/{member}/team',            'Move a member to another team'],
            ['GET',    '/api/projects',                         'List projects (paginated)'],
            ['GET',    '/api/projects/{project}/members',       'Members assigned to a project'],
            ['POST',   '/api/projects/{project}/members/{member}', 'Assign a member to a project'],
        ] as [$verb, $path, $desc])
            <li>
                <span class="verb">{{ $verb }}</span>
                <span class="path"><code>{{ $path }}</code></span>
                <span class="desc">{{ $desc }}</span>
            </li>
        @endforeach
    </ul>

    <footer>PUT/PATCH and DELETE are available on each resource. See the README for the full route table.</footer>
</main>
</body>
</html>
