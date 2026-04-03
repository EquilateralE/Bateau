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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vide Grenier en Ligne</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #f5f0e8;
            --brown: #3d2b1f;
            --rust: #c0623a;
            --sand: #d4b896;
            --dark: #1a1208;
            --tag-bg: #ede4d3;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--cream);
            color: var(--brown);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
        }

        /* Texture de fond */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect width='4' height='4' fill='%23f5f0e8'/%3E%3Ccircle cx='1' cy='1' r='0.5' fill='%23d4b89620'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        header {
            position: relative;
            z-index: 1;
            border-bottom: 2px solid var(--brown);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--brown);
            color: var(--cream);
        }

        .header-left {
            padding: 1.5rem 0;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .logo span {
            color: var(--sand);
        }

        .tagline {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--sand);
            margin-top: 4px;
        }

        .env-badge {
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            padding: 4px 12px;
            border-radius: 2px;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            background: <?= $env === 'production' ? '#c0623a' : '#4a7c59' ?>;
            color: white;
        }

        main {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .section-header {
            display: flex;
            align-items: baseline;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--sand);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-style: italic;
            color: var(--brown);
        }

        .count {
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            color: var(--rust);
            background: var(--tag-bg);
            padding: 2px 8px;
            border-radius: 2px;
        }

        .error-box {
            background: #ffeaea;
            border: 1px solid #e57373;
            border-radius: 4px;
            padding: 1rem 1.5rem;
            font-family: 'DM Mono', monospace;
            font-size: 0.8rem;
            color: #c62828;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: #fff9f0;
            border: 1px solid var(--sand);
            border-radius: 4px;
            padding: 1.5rem;
            position: relative;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 4px 4px 0 var(--sand);
        }

        .card-number {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            color: var(--sand);
            margin-bottom: 0.75rem;
            letter-spacing: 0.1em;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .card-desc {
            font-size: 0.85rem;
            color: #6b5a4e;
            line-height: 1.5;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px dashed var(--sand);
        }

        .price {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--rust);
        }

        .price::after { content: ' €'; font-size: 1rem; }

        .seller {
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            color: #8a7060;
            letter-spacing: 0.05em;
        }

        .seller::before { content: '— '; }

        .date {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            color: var(--sand);
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            border-top: 1px solid var(--sand);
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            color: var(--sand);
        }

        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
            .logo { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <div class="logo">Vide<span>Grenier</span></div>
            <div class="tagline">Marché en ligne · Trésors d'occasion</div>
        </div>
        <div class="env-badge"><?= htmlspecialchars($env) ?></div>
    </header>

    <main>
        <?php if ($error): ?>
            <div class="error-box">
                ⚠ Connexion BDD impossible : <?= htmlspecialchars($error) ?>
            </div>
        <?php else: ?>
            <div class="section-header">
                <h2 class="section-title">Annonces du moment</h2>
                <span class="count"><?= count($annonces) ?> articles</span>
            </div>

            <div class="grid">
                <?php foreach ($annonces as $i => $a): ?>
                <div class="card">
                    <div class="card-number">#<?= str_pad($a['id'], 3, '0', STR_PAD_LEFT) ?></div>
                    <div class="date"><?= date('d/m/Y', strtotime($a['created_at'])) ?></div>
                    <div class="card-title"><?= htmlspecialchars($a['titre']) ?></div>
                    <div class="card-desc"><?= htmlspecialchars($a['description']) ?></div>
                    <div class="card-footer">
                        <span class="price"><?= number_format($a['prix'], 2, ',', ' ') ?></span>
                        <span class="seller"><?= htmlspecialchars($a['vendeur']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        Vide Grenier en Ligne · Environnement : <?= htmlspecialchars($env) ?> · PHP <?= PHP_VERSION ?>
    </footer>
</body>
</html>