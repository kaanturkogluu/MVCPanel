<?php
header('Content-Type: application/json');

// Hata raporlamayı aktif et
error_reporting(E_ALL);
ini_set('display_errors', 1);

// JSON verisini al
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Gerekli dosya yolları
$vt_file = __DIR__ . '/vt.html';
$backup_dir = __DIR__ . '/backups';

// Backup dizinini oluştur (yoksa)
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// Silme işlemi
if (isset($data['action']) && $data['action'] === 'delete') {
    if (!isset($data['section'])) {
        echo json_encode(['success' => false, 'message' => 'Bölüm ID\'si gerekli']);
        exit;
    }

    // Dosyayı yedekle
    $backup_file = $backup_dir . '/vt_' . date('Y-m-d_H-i-s') . '.html';
    copy($vt_file, $backup_file);

    // HTML içeriğini oku
    $html = file_get_contents($vt_file);
    
    // DOM oluştur
    $dom = new DOMDocument();
    @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
    // Silinecek bölümü bul
    $section = $dom->getElementById($data['section']);
    if ($section) {
        // Bölümü sil
        $section->parentNode->removeChild($section);
        
        // Sidebar linkini bul ve sil
        $xpath = new DOMXPath($dom);
        $link = $xpath->query("//a[@href='#" . $data['section'] . "']")->item(0);
        if ($link) {
            $link->parentNode->removeChild($link);
        }
        
        // Değişiklikleri kaydet
        $new_html = $dom->saveHTML();
        if (file_put_contents($vt_file, $new_html)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Dosya yazma hatası']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Bölüm bulunamadı']);
    }
    exit;
}

// Normal kaydetme işlemi
if (!isset($data['section']) || !isset($data['content']) || !isset($data['sidebar_title'])) {
    echo json_encode(['success' => false, 'message' => 'Eksik veri']);
    exit;
}

// Dosyayı yedekle
$backup_file = $backup_dir . '/vt_' . date('Y-m-d_H-i-s') . '.html';
copy($vt_file, $backup_file);

// HTML içeriğini oku
$html = file_get_contents($vt_file);

// DOM oluştur
$dom = new DOMDocument();
@$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

// Mevcut bölümü bul
$section = $dom->getElementById($data['section']);

if ($section) {
    // Mevcut bölümü güncelle
    $section->setAttribute('id', $data['section']);
    $section->nodeValue = ''; // İçeriği temizle
    $fragment = $dom->createDocumentFragment();
    @$fragment->appendXML($data['content']);
    $section->appendChild($fragment);
} else {
    // Yeni bölüm oluştur
    $main_content = $dom->getElementById('main-content');
    if ($main_content) {
        $new_section = $dom->createElement('section');
        $new_section->setAttribute('id', $data['section']);
        $fragment = $dom->createDocumentFragment();
        @$fragment->appendXML($data['content']);
        $new_section->appendChild($fragment);
        $main_content->appendChild($new_section);
    }
}

// Sidebar'ı güncelle
$xpath = new DOMXPath($dom);
$sidebar = $xpath->query("//div[contains(@class, 'sidebar')]")->item(0);
if ($sidebar) {
    $nav = $xpath->query(".//nav", $sidebar)->item(0);
    if ($nav) {
        // Mevcut linki bul
        $link = $xpath->query(".//a[@href='#" . $data['section'] . "']", $nav)->item(0);
        
        if ($link) {
            // Mevcut linki güncelle
            $link->textContent = $data['sidebar_title'];
        } else {
            // Yeni link oluştur
            $new_link = $dom->createElement('a');
            $new_link->setAttribute('href', '#' . $data['section']);
            $new_link->setAttribute('class', 'nav-link');
            $new_link->textContent = $data['sidebar_title'];
            
            $li = $dom->createElement('li');
            $li->setAttribute('class', 'nav-item');
            $li->appendChild($new_link);
            
            $nav->appendChild($li);
        }
    }
}

// Değişiklikleri kaydet
$new_html = $dom->saveHTML();
if (file_put_contents($vt_file, $new_html)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Dosya yazma hatası']);
}
?> 