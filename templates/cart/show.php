    <section class="inner-hero section dark-background">
    </section>
    <section class="section">
        <div class="container">
            <h2 class="mb-4">Votre panier</h2>

            <?php if (empty($cart)): ?>
                <p class="text-muted">Votre panier est vide.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Pizza</th>
                                <th>Taille</th>
                                <th>Qté</th>
                                <th>PU</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grand = 0;
                            foreach ($cart as $line):
                                $grand += (int)$line['totalCents'];
                            ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($line['name']) ?></strong>
                                        <?php if (!empty($line['extras'])): ?>
                                            <div class="small text-muted">+
                                                <?php
                                                $names = array_map(fn($e) => $e['name'], $line['extras']);
                                                echo htmlspecialchars(implode(', ', $names));
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($line['size']) ?></td>
                                    <td><?= (int)$line['qty'] ?></td>
                                    <td><?= number_format(((int)$line['unitCents']) / 100, 2, ',', ' ') ?> €</td>
                                    <td><?= number_format(((int)$line['totalCents']) / 100, 2, ',', ' ') ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th><?= number_format($grand / 100, 2, ',', ' ') ?> €</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>