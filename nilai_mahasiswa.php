<?php
// Escape output agar aman dari XSS
function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function hitungNilaiHuruf($nilai)
{
    if ($nilai >= 85) return "A";
    if ($nilai >= 75) return "B";
    if ($nilai >= 65) return "C";
    if ($nilai >= 50) return "D";
    return "E";
}

$nim = $nama = $prodi = "";
$nilai_tugas = $nilai_uts = $nilai_uas = 0.0;
$nilai_akhir = 0.0;
$nilai_huruf = $status = $pesan_error = "";
$hasil = false;

// Daftar input nilai beserta bobotnya (dipakai di form)
$input_nilai = [
    'nilai_tugas' => 'Nilai Tugas',
    'nilai_uts'   => 'Nilai UTS',
    'nilai_uas'   => 'Nilai UAS',
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim   = trim($_POST["nim"] ?? "");
    $nama  = trim($_POST["nama"] ?? "");
    $prodi = trim($_POST["prodi"] ?? "");

    $nilai_tugas = $_POST["nilai_tugas"] ?? "";
    $nilai_uts   = $_POST["nilai_uts"] ?? "";
    $nilai_uas   = $_POST["nilai_uas"] ?? "";

    $valid = $nim !== "" && $nama !== "" && $prodi !== ""
        && $nilai_tugas !== "" && $nilai_uts !== "" && $nilai_uas !== ""
        && is_numeric($nilai_tugas) && is_numeric($nilai_uts) && is_numeric($nilai_uas);

    if (!$valid) {
        $pesan_error = "Mohon isi semua data dengan benar.";
    } else {
        $nilai_tugas = (float) $nilai_tugas;
        $nilai_uts   = (float) $nilai_uts;
        $nilai_uas   = (float) $nilai_uas;

        $di_rentang = true;
        foreach ([$nilai_tugas, $nilai_uts, $nilai_uas] as $n) {
            if ($n < 0 || $n > 100) {
                $di_rentang = false;
            }
        }

        if (!$di_rentang) {
            $pesan_error = "Nilai harus berada di antara 0 sampai 100.";
        } else {
            $nilai_akhir = array_sum([
                $nilai_tugas * 0.30,
                $nilai_uts * 0.30,
                $nilai_uas * 0.40,
            ]);

            $nilai_huruf = hitungNilaiHuruf($nilai_akhir);
            $status      = $nilai_akhir >= 65 ? "LULUS" : "TIDAK LULUS";
            $hasil       = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Perhitungan Nilai Mahasiswa</title>

    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Cambria, Georgia, serif;
            background: linear-gradient(135deg, #F8F5FF, #EDE5FC, #F8F5FF);
            margin: 0;
            padding: 35px 15px;
            color: #55476D;
            min-height: 100vh;
        }

        .container {
            max-width: 650px;
            width: 100%;
            margin: auto;
            background-color: #FFFFFF;
            padding: 35px;
            border-radius: 20px;
            border: 1px solid #E6D9FA;
            box-shadow: 0 10px 30px rgba(120, 91, 165, 0.12);
        }

        h1 {
            font-family: 'League Spartan', sans-serif;
            color: #7652A8;
            text-align: center;
            font-size: 34px;
            line-height: 1.2;
            margin-top: 0;
            margin-bottom: 12px;
        }

        .deskripsi {
            text-align: center;
            color: #9485A8;
            font-size: 15px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 8px;
            font-size: 17px;
            font-weight: bold;
            color: #725A94;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1.5px solid #D6C4F3;
            border-radius: 9px;
            background-color: #F7F2FF;
            color: #57446F;
            font-family: Cambria, Georgia, serif;
            font-size: 16px;
            outline: none;
            transition: 0.2s;
        }

        input:focus {
            border-color: #A78BDA;
            background-color: #FFFFFF;
            box-shadow: 0 0 0 3px #EEE5FC;
        }

        input::placeholder {
            color: #B4A5CA;
        }

        button {
            width: 100%;
            margin-top: 28px;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #B39AE0, #9273C9);
            color: #FFFFFF;
            font-family: 'League Spartan', sans-serif;
            font-size: 19px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: linear-gradient(135deg, #A087D5, #7958B2);
            transform: translateY(-2px);
            box-shadow: 0 5px 12px #D8C9EF;
        }

        .hasil {
            margin-top: 30px;
            padding: 22px;
            background-color: #F8F4FF;
            border: 1px solid #E1D2F7;
            border-radius: 14px;
        }

        .hasil h2 {
            font-family: 'League Spartan', sans-serif;
            text-align: center;
            color: #7652A8;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .baris {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #E8DFF4;
            font-size: 16px;
        }

        .baris span {
            color: #796B8C;
        }

        .baris strong {
            color: #62468F;
            text-align: right;
            overflow-wrap: anywhere;
        }

        .nilai-akhir {
            margin-top: 15px;
            padding: 17px;
            border-radius: 10px;
            background-color: #E9DFFC;
            text-align: center;
        }

        .nilai-akhir span {
            display: block;
            color: #79629E;
            margin-bottom: 8px;
        }

        .nilai-akhir strong {
            font-family: 'League Spartan', sans-serif;
            color: #7652A8;
            font-size: 35px;
        }

        .status {
            text-align: center;
            margin-top: 15px;
            padding: 12px;
            border-radius: 8px;
            background-color: #E8DFFC;
            color: #684995;
            font-weight: bold;
        }

        .error {
            margin-top: 20px;
            padding: 13px;
            border-radius: 8px;
            background-color: #FFF0F0;
            color: #B42318;
            border: 1px solid #F5C6C6;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            color: #A99AB9;
            font-size: 13px;
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 12px;
            }

            .container {
                padding: 22px 18px;
            }

            h1 {
                font-size: 28px;
            }

            .baris {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Perhitungan Nilai Mahasiswa</h1>

    <p class="deskripsi">
        Masukkan data mahasiswa untuk menghitung nilai akhir.
    </p>

    <form method="POST" action="">

        <label for="nim">NIM</label>
        <input type="text" id="nim" name="nim" placeholder="Masukkan NIM"
               value="<?= e($nim) ?>" required>

        <label for="nama">Nama Mahasiswa</label>
        <input type="text" id="nama" name="nama" placeholder="Masukkan nama mahasiswa"
               value="<?= e($nama) ?>" required>

        <label for="prodi">Program Studi</label>
        <input type="text" id="prodi" name="prodi" placeholder="Masukkan program studi"
               value="<?= e($prodi) ?>" required>

        <?php foreach ($input_nilai as $name => $label) { ?>
            <label for="<?= $name ?>"><?= $label ?></label>
            <input type="number" id="<?= $name ?>" name="<?= $name ?>" placeholder="0 - 100"
                   min="0" max="100" step="any"
                   value="<?= e($_POST[$name] ?? '') ?>" required>
        <?php } ?>

        <button type="submit">Hitung Nilai</button>

    </form>

    <?php if ($pesan_error != "") { ?>
        <div class="error"><?= e($pesan_error) ?></div>
    <?php } ?>

    <?php if ($hasil) { ?>
        <div class="hasil">

            <h2>Hasil Perhitungan</h2>

            <div class="baris">
                <span>NIM</span>
                <strong><?= e($nim) ?></strong>
            </div>

            <div class="baris">
                <span>Nama Mahasiswa</span>
                <strong><?= e($nama) ?></strong>
            </div>

            <div class="baris">
                <span>Program Studi</span>
                <strong><?= e($prodi) ?></strong>
            </div>

            <div class="baris">
                <span>Nilai Tugas</span>
                <strong><?= $nilai_tugas ?></strong>
            </div>

            <div class="baris">
                <span>Nilai UTS</span>
                <strong><?= $nilai_uts ?></strong>
            </div>

            <div class="baris">
                <span>Nilai UAS</span>
                <strong><?= $nilai_uas ?></strong>
            </div>

            <div class="nilai-akhir">
                <span>Nilai Akhir</span>
                <strong><?= number_format($nilai_akhir, 2, ',', '.') ?></strong>
            </div>

            <div class="baris">
                <span>Nilai Huruf</span>
                <strong><?= $nilai_huruf ?></strong>
            </div>

            <div class="status">Status: <?= $status ?></div>

        </div>
    <?php } ?>

    <div class="footer">

</div>

</body>
</html>