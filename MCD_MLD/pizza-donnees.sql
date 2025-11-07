-- ======================================================
-- SEED CATALOG — PIZZA-APP (Ingredients + Pizzas + Recettes)
-- Hypothèse: table pizza_ingredient(id_ingredient, id_pizza, quantityUnit)
-- ======================================================

START TRANSACTION;

-- ============= INGREDIENTS =============
INSERT INTO ingredient (name, unit, isVegetarian, isVegan, hasAllergens, extraPriceCents) VALUES
-- Fromages
('Tomates San Marzano', 'GRAM', 1, 1, 0, NULL),
('Parmigiano Reggiano DOP', 'GRAM', 1, 0, 1, 150),
('Fior di latte', 'GRAM', 1, 0, 1, 150),
('Spianata piccante', 'GRAM', 0, 0, 0, 200),
('Ricotta fraîche', 'GRAM', 1, 0, 1, 150),
('Oignons confits', 'GRAM', 1, 1, 0, 100),
('Poivrons doux séchés', 'GRAM', 1, 1, 0, 100),
('Taleggio DOP', 'GRAM', 1, 0, 1, 150),
('Gorgonzola', 'GRAM', 1, 0, 1, 150),
('Fior di latte fumé', 'GRAM', 1, 0, 1, 150),
('Miel à la truffe', 'GRAM', 1, 0, 0, 250),
('Tomates datterini', 'GRAM', 1, 1, 0, 100),
('Pesto de basilic', 'GRAM', 1, 0, 1, 100),
('Grana Padano', 'GRAM', 1, 0, 1, 150),
('Basilic', 'GRAM', 1, 1, 0, 100),
('Base blanche Fior di latte', 'GRAM', 1, 0, 1, NULL),
('Tomates confites', 'GRAM', 1, 1, 0, 100),
('Aubergines frites', 'GRAM', 1, 1, 0, 100),
('Ricotta salée', 'GRAM', 1, 0, 1, 150),
('Bufala', 'GRAM', 1, 0, 1, 150),
('Provolone', 'GRAM', 1, 0, 1, 150),
('Jambon de Parme 24 mois', 'GRAM', 0, 0, 0, 200),
('Sauce tomate jaune', 'GRAM', 1, 1, 0, 100),
('Mozzarella di Bufala', 'GRAM', 1, 0, 1, 150),
('Amandes grillées', 'GRAM', 1, 1, 1, 250),
('Confiture de figues', 'GRAM', 1, 1, 0, 250),
('Champignons de Paris sautés', 'GRAM', 1, 1, 0, NULL),
('Jambon cuit Gran Biscotto', 'GRAM', 0, 0, 0, 200),
('Persil', 'GRAM', 1, 1, 0, 100),
('Olives taggiasche', 'GRAM', 1, 1, 0, 100),
('Anchois de Santoña', 'GRAM', 0, 0, 0, 200),
('Câpres de Pantelleria', 'GRAM', 1, 1, 0, 100),
('Filets de thon à l''huile d''olive', 'GRAM', 0, 0, 0, 200),
('Sauce tomate San Marzano cuite 4h', 'GRAM', 1, 1, 0, 100),
('Noisettes', 'GRAM', 1, 1, 1, 250),
('Provola fumée', 'GRAM', 1, 0, 1, 150),
('Crème', 'ML', 1, 0, 1, 100),
('Jambon speck', 'GRAM', 0, 0, 0, 200),
('Mozzarella Casa Azzurra', 'GRAM', 1, 0, 1, 150),
('Stracciatella', 'GRAM', 1, 0, 1, 150),
('Crème de truffe', 'ML', 1, 0, 1, 100),
('Truffe fraîche', 'GRAM', 1, 1, 0, 250),
('Origan', 'GRAM', 1, 1, 0, 100),
('Ail', 'GRAM', 1, 1, 0, 100),
('Chips de courgettes', 'GRAM', 1, 1, 0, 100),
('Fleurs de courgettes', 'GRAM', 1, 1, 0, 100),
('Crème de Provolone', 'ML', 1, 0, 1, 100),
('Crème de courgettes', 'ML', 1, 1, 0, 100),
('Noix', 'GRAM', 1, 1, 1, 250),
('Sauce tomate', 'ML', 1, 1, 0, 100);

-- ============= PIZZAS =============
INSERT INTO pizza (name, slug, description, photo, basePriceCents, isRecommended, filter) VALUES
-- CLASSIC
('Margherita', 'margherita',
 'Tomates, Fior di Latte, Parmigiano Reggiano DOP et basilic.',
 'margherita.webp', 1300, 0, 'filter-classic'),

('Regina', 'regina',
 'Sauce tomate, Fior di latte, champignons de Paris sautés, jambon cuit Gran Biscotto et persil.',
 'regina.webp', 1600, 0, 'filter-classic'),

('Margherita Bufala', 'margherita-bufala',
 'Tomates, Bufala, Parmigiano Reggiano DOP et basilic.',
 'margherita-bufala.webp', 1600, 0, 'filter-classic'),

('Napoletana', 'napoletana',
 'Pizza fine et croustillante garnie de tranches de tomates fraîches, de basilic, d''origan, d''ail, d''anchois de Santoña, de câpres de Pantelleria et de mozzarella di Bufala fondante.',
 'napoletana.webp', 1600, 0, 'filter-classic'),

-- VEGETARIAN
('Burrata Multicolore', 'burrata-multicolore',
 'Pesto au basilic, fior di latte, tomates confites, stracciatella et amandes.',
 'burrata-multicolore.webp', 1700, 0, 'filter-vegetarian'),

('Montanara', 'montanara',
 'Pizza double cuisson frite puis passée au four, garnie de sauce tomate San Marzano cuite 4h, Mozzarella di Bufala, tomates confites, Grana Padano et basilic.',
 'montanara.webp', 1600, 0, 'filter-vegetarian'),

('Quattro Formaggi e Miele', 'quattro-formaggi-e-miele',
 'Ricotta, Taleggio DOP, Gorgonzola, Fior di latte fumé, noix, miel à la truffe et persil.',
 'quattro-formaggi-e-miele.webp', 1700, 0, 'filter-vegetarian'),

('Tricolore', 'tricolore',
 'Base blanche Fior di latte, tomates datterini, pesto de basilic, Grana Padano et basilic.',
 'tricolore.webp', 1400, 0, 'filter-vegetarian'),

('Norma', 'norma',
 'Sauce tomate, Fior di latte fumé, tomates confites, aubergines frites et Ricotta salée.',
 'norma.webp', 1600, 0, 'filter-vegetarian'),

-- SPECIAL
('Campione del Mondo', 'campione-del-mondo',
 'Sauce tomate jaune, jambon de Parme 24 mois, Mozzarella di Bufala, amandes grillées, Provolone, confiture de figues.',
 'campione-del-mondo.webp', 2000, 1, 'filter-special'),

('Piccante Calabrese', 'piccante-calabrese',
 'Sauce tomate, Fior di latte, Ricotta fraîche, Spianata piccante, oignons confits et poivrons doux séchés.',
 'piccante-calabrese.webp', 1700, 1, 'filter-special'),

('Tartufo', 'tartufo',
 'Crème de truffe, Stracciatella, Fior di latte fumé, noisettes, champignons de Paris sautés, persil et truffe fraîche.',
 'tartufo.webp', 2000, 0, 'filter-special'),

('Nerano', 'nerano',
 'Crème de courgettes, Fior di latte, chips de courgettes, fleurs de courgettes et crème de Provolone.',
 'nerano.webp', 1700, 0, 'filter-special');

-- ============= RECETTES (pizza_ingredient) =============
-- NB: quantityUnit = valeur indicative (g/ml). Ajuste plus tard si besoin.

-- Margherita
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Margherita' AND i.name IN ('Tomates San Marzano','Fior di latte','Parmigiano Reggiano DOP','Basilic');

-- Regina
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Regina' AND i.name IN ('Sauce tomate','Fior di latte','Champignons de Paris sautés','Jambon cuit Gran Biscotto','Persil');

-- Margherita Bufala
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Margherita Bufala' AND i.name IN ('Tomates San Marzano','Bufala','Parmigiano Reggiano DOP','Basilic');

-- Napoletana
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, CASE i.name
  WHEN 'Tomates San Marzano' THEN 120
  WHEN 'Mozzarella di Bufala' THEN 100
  WHEN 'Anchois de Santoña' THEN 25
  WHEN 'Câpres de Pantelleria' THEN 10
  WHEN 'Basilic' THEN 3
  WHEN 'Origan' THEN 2
  WHEN 'Ail' THEN 5
  ELSE 80
END
FROM ingredient i, pizza p
WHERE p.name='Napoletana' AND i.name IN ('Tomates San Marzano','Mozzarella di Bufala','Anchois de Santoña','Câpres de Pantelleria','Basilic','Origan','Ail');

-- Burrata Multicolore
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Burrata Multicolore' AND i.name IN ('Pesto de basilic','Fior di latte','Tomates confites','Stracciatella','Amandes grillées');

-- Montanara
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Montanara' AND i.name IN ('Sauce tomate San Marzano cuite 4h','Mozzarella di Bufala','Tomates confites','Grana Padano','Basilic');

-- Quattro Formaggi e Miele
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Quattro Formaggi e Miele' AND i.name IN ('Ricotta salée','Taleggio DOP','Gorgonzola','Fior di latte fumé','Noix','Miel à la truffe','Persil');

-- Tricolore
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Tricolore' AND i.name IN ('Base blanche Fior di latte','Tomates datterini','Pesto de basilic','Grana Padano','Basilic');

-- Norma
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Norma' AND i.name IN ('Sauce tomate','Fior di latte fumé','Tomates confites','Aubergines frites','Ricotta salée');

-- Campione del Mondo
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Campione del Mondo' AND i.name IN ('Sauce tomate jaune','Jambon de Parme 24 mois','Mozzarella di Bufala','Amandes grillées','Provolone','Confiture de figues');

-- Piccante Calabrese
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, 100 FROM ingredient i, pizza p
WHERE p.name='Piccante Calabrese' AND i.name IN ('Sauce tomate','Fior di latte','Ricotta fraîche','Spianata piccante','Oignons confits','Poivrons doux séchés');

-- Tartufo
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, CASE i.name
  WHEN 'Crème de truffe' THEN 40
  WHEN 'Stracciatella' THEN 70
  WHEN 'Fior di latte fumé' THEN 80
  WHEN 'Noisettes' THEN 20
  WHEN 'Champignons de Paris sautés' THEN 80
  WHEN 'Persil' THEN 3
  WHEN 'Truffe fraîche' THEN 10
  ELSE 80
END
FROM ingredient i, pizza p
WHERE p.name='Tartufo' AND i.name IN ('Crème de truffe','Stracciatella','Fior di latte fumé','Noisettes','Champignons de Paris sautés','Persil','Truffe fraîche');

-- Nerano
INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
SELECT i.id, p.id, CASE i.name
  WHEN 'Crème de courgettes' THEN 60
  WHEN 'Fior di latte' THEN 100
  WHEN 'Chips de courgettes' THEN 50
  WHEN 'Fleurs de courgettes' THEN 30
  WHEN 'Crème de Provolone' THEN 40
  ELSE 80
END
FROM ingredient i, pizza p
WHERE p.name='Nerano' AND i.name IN ('Crème de courgettes','Fior di latte','Chips de courgettes','Fleurs de courgettes','Crème de Provolone');

COMMIT;
