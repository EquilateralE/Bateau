<?php
$host = 'db';
$dbname = 'vide_grenier';
$user = 'vg_user';
$pass = 'vg_pass';

$pdo = null;
$error = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = $e->getMessage();
}

$annonces = [];
if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM annonces ORDER BY created_at DESC");
    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$env = getenv('APP_ENV') ?: 'development';
$isProd = $env === 'production';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vide Grenier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: #0f0f0f;
            --surface: #1a1a1a;
            --surface2: #242424;
            --border: #2e2e2e;
            --text: #f0f0f0;
            --muted: #888;
            --accent: #e8ff47;
            --prod: #ff6b35;
            --dev: #47e8a0;
            --radius: 14px;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            height: 64px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            background: rgba(15,15,15,0.85);
            backdrop-filter: blur(12px);
            z-index: 100;
        }

        .logo {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text);
        }

        .logo em { color: var(--accent); font-style: normal; }

        .env-pill {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 999px;
        }

        .env-pill.prod {
            background: rgba(255,107,53,0.15);
            color: var(--prod);
            border: 1px solid rgba(255,107,53,0.3);
        }

        .env-pill.dev {
            background: rgba(71,232,160,0.12);
            color: var(--dev);
            border: 1px solid rgba(71,232,160,0.25);
        }

        .hero {
            padding: 4rem 2.5rem 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .hero-tag::before {
            content: '';
            display: block;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--accent);
        }

        .hero h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -1.5px;
            margin-bottom: 1rem;
        }

        .hero h1 span { color: var(--accent); }

        .hero-sub {
            color: var(--muted);
            font-size: 1rem;
            font-weight: 300;
        }

        .stats-bar {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2.5rem 2.5rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .stat {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .stat-value {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .grid-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2.5rem 4rem;
        }

        .grid-label {
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 1px;
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .card {
            background: var(--surface);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: background 0.15s;
        }

        .card:hover { background: var(--surface2); }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.25rem;
        }

        .card-id, .card-date {
            font-size: 0.68rem;
            color: var(--muted);
            letter-spacing: 0.05em;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.3;
        }

        .card-desc {
            font-size: 0.83rem;
            color: var(--muted);
            font-weight: 300;
            line-height: 1.5;
            flex: 1;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border);
        }

        .price {
            font-family: 'Syne', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent);
        }

        .seller {
            font-size: 0.75rem;
            color: var(--muted);
            background: var(--surface2);
            padding: 3px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
        }

        .error-box {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2.5rem;
        }

        .error-inner {
            background: rgba(255,80,80,0.08);
            border: 1px solid rgba(255,80,80,0.25);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            font-size: 0.83rem;
            color: #ff7070;
        }

        footer {
            border-top: 1px solid var(--border);
            padding: 1.5rem 2.5rem;
            display: flex;
            justify-content: space-between;
            font-size: 0.72rem;
            color: var(--muted);
        }

        @media (max-width: 640px) {
            header, .hero, .stats-bar, .grid-wrap, footer { padding-left: 1.25rem; padding-right: 1.25rem; }
            .hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Vide<em>Grenier</em></div>
    <span class="env-pill <?= $isProd ? 'prod' : 'dev' ?>"><?= htmlspecialchars($env) ?></span>
</header>

<section class="hero">
    <div class="hero-tag">Marché en ligne</div>
    <h1>Trésors <span>d'occasion</span>,<br>petits prix.</h1>
    <p class="hero-sub">Achetez et vendez vos objets facilement.</p>
</section>

<?php if ($error): ?>
<div class="error-box">
    <div class="error-inner">Connexion BDD impossible — <?= htmlspecialchars($error) ?></div>
</div>
<?php else: ?>

<div class="stats-bar">
    <div class="stat">
        <div class="stat-value"><?= count($annonces) ?></div>
        <div class="stat-label">annonces actives</div>
    </div>
    <?php $total = array_sum(array_column($annonces, 'prix')); $vendeurs = count(array_unique(array_column($annonces, 'vendeur'))); ?>
    <div class="stat">
        <div class="stat-value"><?= number_format($total, 0, ',', ' ') ?> €</div>
        <div class="stat-label">valeur totale</div>
    </div>
    <div class="stat">
        <div class="stat-value"><?= $vendeurs ?></div>
        <div class="stat-label">vendeurs</div>
    </div>
</div>

<div class="grid-wrap">
    <div class="grid-label">Toutes les annonces</div>
    <div class="grid">
        <?php foreach ($annonces as $a): ?>
        <div class="card">
            <div class="card-top">
                <span class="card-id">#<?= str_pad($a['id'], 3, '0', STR_PAD_LEFT) ?></span>
                <span class="card-date"><?= date('d/m/Y', strtotime($a['created_at'])) ?></span>
            </div>
            <div class="card-title"><?= htmlspecialchars($a['titre']) ?></div>
            <div class="card-desc"><?= htmlspecialchars($a['description']) ?></div>
            <div class="card-footer">
                <span class="price"><?= number_format($a['prix'], 2, ',', ' ') ?> €</span>
                <span class="seller"><?= htmlspecialchars($a['vendeur']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php endif; ?>

<footer>
    <span>Vide Grenier &copy; <?= date('Y') ?></span>
    <span>PHP <?= PHP_VERSION ?> · <?= htmlspecialchars($env) ?></span>
</footer>

</body>
</html>
