<?php
header('Content-Type: text/html; charset=UTF-8');

// Veritabanı bağlantı bilgileri
$hostname = "localhost";  
$username = "root";       
$password = "";   
$database = "student_registration";  

// Veritabanı bağlantısını kurma
$conn = mysqli_connect($hostname, $username, $password, $database);

// Bağlantı başarılı mı diye kontrol etme
if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

// "students" tablosunun var olup olmadığını kontrol etme
$table_exists_query = "SHOW TABLES LIKE 'students'";
$table_exists_result = mysqli_query($conn, $table_exists_query);

if (mysqli_num_rows($table_exists_result) == 0) {
    // "students" tablosu yoksa oluşturma
    $create_table_query = "CREATE TABLE students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255),
        email VARCHAR(255),
        gender ENUM('Male', 'Female')
    )";

    // "students" tablosunu oluşturma sorgusunu çalıştırma
    if (mysqli_query($conn, $create_table_query)) {
        echo "Tablo 'students' başarıyla oluşturuldu.<br>";
    } else {
        echo "Tablo oluşturma hatası: " . mysqli_error($conn) . "<br>";
    }
}

// Form verisi gönderildi mi diye kontrol etme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form verilerini al
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $gender = $_POST["gender"];

    // Form verilerini doğrulama
    $errors = array(); // Doğrulama hatalarını saklamak için dizi

    // Ad soyad doğrulaması
    if (empty($full_name)) {
        $errors[] = "Ad Soyad alanı zorunludur.";
    }

    // E-posta doğrulaması
    if (empty($email)) {
        $errors[] = "E-posta Adresi alanı zorunludur.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Geçersiz E-posta Adresi.";
    }

    // Cinsiyet doğrulaması
    $allowed_genders = array("Erkek", "Kadın");
    if (empty($gender) || !in_array($gender, $allowed_genders)) {
        $errors[] = "Lütfen geçerli bir cinsiyet seçin.";
    }

    // Doğrulama hataları var mı diye kontrol etme
    if (!empty($errors)) {
        // Doğrulama hatalarını ekranda gösterme
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    } else {
        // Form verilerini "students" tablosuna ekleme
        $insert_query = "INSERT INTO students (full_name, email, gender) VALUES ('$full_name', '$email', '$gender')";
        if (mysqli_query($conn, $insert_query)) {
            echo "Öğrenci bilgileri başarıyla eklendi.<br>";
            echo '<a href="index.html"><button>Kayda Geri Dön</button></a>';
        } else {
            echo "Öğrenci bilgileri ekleme hatası: " . mysqli_error($conn) . "<br>";
        }
    }
}

// Kayıtlı öğrencilerin bilgilerini getirip gösterme
$select_query = "SELECT * FROM students";
$result = mysqli_query($conn, $select_query);

if (mysqli_num_rows($result) > 0) {
    echo "<h2>Kayıtlı Öğrenciler:</h2>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row["id"] . "<br>";
        echo "Ad Soyad: " . $row["full_name"] . "<br>";
        echo "E-posta: " . $row["email"] . "<br>";
        echo "Cinsiyet: " . $row["gender"] . "<br><br>";
    }
} else {
    echo "Kayıtlı öğrenci bulunmamaktadır.<br>";
}

// Veritabanı bağlantısını kapatma
mysqli_close($conn);
?>
