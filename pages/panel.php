<?php
$siteTitle = "Ana Panel Ekranı ";
require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/sidebar.php";


?>


<!-- Main Content -->
<div class="main-content">
 
<?php 
require_once __DIR__."/../includes/navbar.php";
?>
    <!-- Dashboard Content -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon bg-primary-light text-primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="title">Toplam Kullanıcı</div>
                        <div class="value">1,234</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon bg-success-light text-success">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <div class="title">Toplam Doküman</div>
                        <div class="value">567</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon bg-warning-light text-warning">
                            <i class="bi bi-eye"></i>
                        </div>
                        <div class="title">Toplam Görüntülenme</div>
                        <div class="value">89.1K</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon bg-danger-light text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="title">Aktif Uyarılar</div>
                        <div class="value">12</div>
                    </div>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Son Kullanıcılar</h5>
                    <button class="btn btn-primary btn-sm">Tümünü Gör</button>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kullanıcı</th>
                                <th>E-posta</th>
                                <th>Durum</th>
                                <th>Kayıt Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder small">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div>Ahmet Yılmaz</div>
                                    </div>
                                </td>
                                <td>ahmet@example.com</td>
                                <td><span class="badge bg-success" data-i18n="active">Aktif</span></td>
                                <td>2024-03-15</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                        data-i18n="edit">Düzenle</button>
                                    <button class="btn btn-sm btn-outline-danger" data-i18n="delete">Sil</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img width="32px" height="32px" src="https://www.shareicon.net/data/512x512/2016/08/05/806962_user_512x512.png" class="rounded-circle me-2"
                                            alt="User">
                                        <div>Ayşe Demir</div>
                                    </div>
                                </td>
                                <td>ayse@example.com</td>
                                <td><span class="badge bg-warning">Beklemede</span></td>
                                <td>2024-03-14</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1">Düzenle</button>
                                    <button class="btn btn-sm btn-outline-danger">Sil</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img width="32px" height="32px" src="https://www.shareicon.net/data/512x512/2016/08/05/806962_user_512x512.png" class="rounded-circle me-2"
                                            alt="User">
                                        <div>Mehmet Kaya</div>
                                    </div>
                                </td>
                                <td>mehmet@example.com</td>
                                <td><span class="badge bg-danger">Pasif</span></td>
                                <td>2024-03-13</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1">Düzenle</button>
                                    <button class="btn btn-sm btn-outline-danger">Sil</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

 

<?php
require_once __DIR__ . "/../includes/footer.php"; ?>