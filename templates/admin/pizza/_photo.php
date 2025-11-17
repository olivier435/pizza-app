<div class="col-md-6">
    <label class="form-label">Photo de la pizza</label>

    <div class="mb-2">
        <?php if (!empty($currentPhoto)): ?>
            <div class="mb-2">
                <img
                    src="/assets/img/restaurant/<?= htmlspecialchars($currentPhoto) ?>"
                    alt="Photo de la pizza"
                    class="img-fluid rounded border"
                    id="photo-preview"
                    style="max-height: 180px; cursor: pointer;"
                >
            </div>
        <?php else: ?>
            <div class="mb-2 text-muted small">
                Aucune image pour le moment. Cliquez sur le champ ci-dessous pour en ajouter une.
            </div>
        <?php endif; ?>
    </div>

    <!-- Nom de fichier actuel (stocké en BDD) -->
    <input type="hidden"
           name="current_photo"
           value="<?= htmlspecialchars($currentPhoto ?? '') ?>">

    <!-- Nouveau fichier -->
    <div class="mb-2">
        <input
            type="file"
            name="photoFile"
            id="photoFile"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            class="form-control <?= isset($errors['photo']) ? 'is-invalid' : '' ?>"
        >
        <?php if (isset($errors['photo'])): ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['photo']) ?>
            </div>
        <?php else: ?>
            <div class="form-text">
                Formats acceptés : JPG, PNG, WebP. Taille minimale : 1024 × 683 px.
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($currentPhoto)): ?>
        <div class="form-check mt-2">
            <input
                class="form-check-input"
                type="checkbox"
                value="1"
                id="remove_photo"
                name="remove_photo"
            >
            <label class="form-check-label" for="remove_photo">
                Supprimer l'image actuelle
            </label>
        </div>
    <?php endif; ?>
</div>