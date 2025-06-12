<?php

class Cleaner
{
    /**
     * String veriyi temizler ve güvenli hale getirir
     * @param string $data
     * @return string
     */
    public static function cleanString($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'cleanString'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    /**
     * SQL injection'a karşı koruma sağlar
     * @param string $data
     * @return string
     */
    public static function sanitizeSQL($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeSQL'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = str_replace(['"', "'", ';', '--', '/*', '*/'], '', $data);
        return $data;
    }

    /**
     * Email adresini doğrular ve temizler
     * @param string $email
     * @return string|false
     */
    public static function cleanEmail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }
        return false;
    }

    /**
     * URL'yi doğrular ve temizler
     * @param string $url
     * @return string|false
     */
    public static function cleanURL($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        return false;
    }

    /**
     * Integer değeri doğrular ve temizler
     * @param mixed $data
     * @return int|false
     */
    public static function cleanInt($data)
    {
        if (is_numeric($data)) {
            return (int) $data;
        }
        return false;
    }

    /**
     * Float değeri doğrular ve temizler
     * @param mixed $data
     * @return float|false
     */
    public static function cleanFloat($data)
    {
        if (is_numeric($data)) {
            return (float) $data;
        }
        return false;
    }

    /**
     * Boolean değeri doğrular ve temizler
     * @param mixed $data
     * @return bool
     */
    public static function cleanBoolean($data)
    {
        return filter_var($data, FILTER_VALIDATE_BOOLEAN);
    }

 

    /**
     * Dosya adını güvenli hale getirir
     * @param string $filename
     * @return string
     */
    public static function cleanFileName($filename)
    {
        // Sadece alfanumerik karakterler, nokta, tire ve alt çizgiye izin ver
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        // Çift uzantıları engelle
        $filename = preg_replace('/\.(?=.*\.)/', '', $filename);
        return $filename;
    }

    /**
     * Telefon numarasını temizler ve formatlar
     * @param string $phone
     * @return string
     */
    public static function cleanPhone($phone)
    {
        // Sadece rakamları al
        $phone = preg_replace('/[^0-9]/', '', $phone);
        // Türkiye telefon numarası formatına uygun hale getir
        if (strlen($phone) === 10) {
            $phone = '90' . $phone;
        }
        return $phone;
    }

    /**
     * Tarih formatını doğrular ve temizler
     * @param string $date
     * @param string $format
     * @return string|false
     */
    public static function cleanDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date ? $date : false;
    }

    /**
     * JSON verisini temizler ve doğrular
     * @param string $json
     * @return array|false
     */
    public static function cleanJSON($json)
    {
        $data = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
        return false;
    }

    /**
     * XSS koruması için özel karakterleri temizler
     * @param string $data
     * @return string
     */
    public static function preventXSS($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'preventXSS'], $data);
        }
        
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Tüm veriyi recursive olarak temizler
     * @param mixed $data
     * @return mixed
     */
    public static function cleanAll($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'cleanAll'], $data);
        }
        
        if (is_string($data)) {
            $data = self::cleanString($data);
            $data = self::preventXSS($data);
        }
        
        return $data;
    }
}
