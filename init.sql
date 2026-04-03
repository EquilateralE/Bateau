CREATE DATABASE IF NOT EXISTS vide_grenier;
USE vide_grenier;

CREATE TABLE IF NOT EXISTS annonces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2),
    vendeur VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO annonces (titre, description, prix, vendeur) VALUES
('Vélo de ville', 'Vélo en bon état, 7 vitesses, couleur rouge', 45.00, 'Marie'),
('Table en bois', 'Table rustique 6 personnes, légère rayure', 80.00, 'Pierre'),
('Appareil photo argentique', 'Canon AE-1, fonctionne parfaitement', 120.00, 'Sophie'),
('Collection de vinyles', '30 vinyles années 80, très bon état', 60.00, 'Jean'),
('Lampe de chevet', 'Lampe vintage, abat-jour intact', 15.00, 'Lucie'),
('Jeux de société', 'Lot de 5 jeux complets : Cluedo, Risk, Monopoly...', 25.00, 'Thomas');
