<?php
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header("Location: $url");
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function upload_image(array $file, string $targetDir, string $relativeDir): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException("Image upload failed.");
    if ($file['size'] > 5 * 1024 * 1024) throw new RuntimeException("Image must be 5MB or smaller.");

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) throw new RuntimeException("Only JPG, PNG, GIF, and WEBP images are allowed.");

    $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $targetDir . DIRECTORY_SEPARATOR . $name)) {
        throw new RuntimeException("Could not save the uploaded image.");
    }
    return $relativeDir . $name;
}

function user_by_id(mysqli $conn, int $id): ?array
{
    $stmt = $conn->prepare("SELECT id,name,email,phone,profile_image,created_at FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $letters = '';
    foreach (array_slice($parts, 0, 2) as $part) $letters .= strtoupper(substr($part, 0, 1));
    return $letters ?: 'U';
}
