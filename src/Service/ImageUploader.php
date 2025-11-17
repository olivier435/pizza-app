<?php
declare(strict_types=1);

namespace App\Service;

final class ImageUploader
{
    private string $uploadDir;
    private string $publicPrefix;

    public function __construct(
        string $uploadDir = __DIR__ . '/../../public/assets/img/restaurant',
        string $publicPrefix = '/assets/img/restaurant'
    ) {
        $this->uploadDir    = rtrim($uploadDir, '/\\');
        $this->publicPrefix = rtrim($publicPrefix, '/');

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    /**
     * Traite l'upload + resize + conversion en WebP.
     *
     * - Si aucun fichier envoyé → renvoie le nom existant (inchangé)
     * - Sinon → vérifie, redimensionne, enregistre & supprime l'ancienne image
     *
     * @param string      $fieldName    Nom du champ file dans le formulaire
     * @param string|null $existing     Nom de fichier déjà en BDD (optionnel)
     * @param string|null $basenameSlug Slug de la pizza (pour le nom du fichier)
     *
     * @throws \RuntimeException
     */
    public function uploadAndResize(string $fieldName, ?string $existing = null, ?string $basenameSlug = null): string
    {
        if (
            !isset($_FILES[$fieldName]) ||
            $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE
        ) {
            // Aucun nouveau fichier → on garde l'existant
            return $existing ?? '';
        }

        $file = $_FILES[$fieldName];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(
                "Erreur lors de l'upload du fichier (code {$file['error']})."
            );
        }

        $tmpPath = $file['tmp_name'];

        $info = @getimagesize($tmpPath);
        if (!$info) {
            throw new \RuntimeException("Le fichier envoyé n'est pas une image valide.");
        }

        [$width, $height] = $info;
        $mime = $info['mime'] ?? '';

        $allowed = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        if (!in_array($mime, $allowed, true)) {
            throw new \RuntimeException(
                "Format non supporté. Formats acceptés : JPG, PNG, WebP."
            );
        }

        if ($width < 1024 || $height < 683) {
            throw new \RuntimeException(
                "Image trop petite. Dimensions minimales : 1024×683 pixels."
            );
        }

        // Création de la ressource source
        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($tmpPath);
                break;
            case 'image/png':
                $src = imagecreatefrompng($tmpPath);
                break;
            case 'image/webp':
                if (!function_exists('imagecreatefromwebp')) {
                    throw new \RuntimeException("Le serveur ne supporte pas WebP.");
                }
                $src = imagecreatefromwebp($tmpPath);
                break;
            default:
                throw new \RuntimeException("Format d'image non supporté.");
        }

        if (!$src) {
            throw new \RuntimeException("Impossible de lire l'image envoyée.");
        }

        // Calcul du resize : on veut au moins 1024×683 sans recadrer
        $scale = max(1024 / $width, 683 / $height);
        $newW  = (int)round($width * $scale);
        $newH  = (int)round($height * $scale);

        $dst = imagecreatetruecolor($newW, $newH);

        // Transparence pour la sortie WebP
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

        // --- Nom de fichier basé sur le slug ---
        $base = $basenameSlug ?: 'pizza';
        $base = mb_strtolower($base, 'UTF-8');
        $base = preg_replace('~[^a-z0-9\-]+~', '-', $base);
        $base = trim($base ?? 'pizza', '-');

        $suffix   = date('His') . '_' . bin2hex(random_bytes(4));
        $filename = sprintf('%s_%s.webp', $base, $suffix);
        $target   = $this->uploadDir . DIRECTORY_SEPARATOR . $filename;

        if (!function_exists('imagewebp')) {
            throw new \RuntimeException("Le serveur ne supporte pas la génération WebP.");
        }

        // Sauvegarde en WebP (qualité 85)
        imagewebp($dst, $target, 85);

        imagedestroy($src);
        imagedestroy($dst);

        // On supprime l'ancienne image éventuelle
        if ($existing) {
            $this->delete($existing);
        }

        return $filename;
    }

    public function delete(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $path = $this->uploadDir . DIRECTORY_SEPARATOR . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    public function getPublicUrl(?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }

        return $this->publicPrefix . '/' . ltrim(basename($filename), '/');
    }
}