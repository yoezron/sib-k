<?php

/**
 * File Path: app/Libraries/ExcelImporter.php
 * 
 * Excel Importer Library
 * Library untuk import data siswa dari file Excel menggunakan PhpSpreadsheet
 * 
 * @package    SIB-K
 * @subpackage Libraries
 * @category   Data Import
 * @author     Development Team
 * @created    2025-01-01
 */

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\RoleModel;

class ExcelImporter
{
    protected $userModel;
    protected $studentModel;
    protected $classModel;
    protected $roleModel;
    protected $db;

    protected $results = [
        'total_rows' => 0,
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->roleModel = new RoleModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Import students from Excel file
     * 
     * @param string $filePath Path to Excel file
     * @param array $options Import options
     * @return array Import results
     */
    public function importStudents($filePath, $options = [])
    {
        try {
            // Reset results
            $this->resetResults();

            // Load Excel file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            // Validate headers
            if (!$this->validateHeaders($worksheet)) {
                throw new \Exception('Format template Excel tidak sesuai. Silakan gunakan template yang disediakan.');
            }

            // Get student role ID
            $studentRole = $this->roleModel->where('role_name', 'Siswa')->first();
            if (!$studentRole) {
                throw new \Exception('Role "Siswa" tidak ditemukan dalam database.');
            }

            // Get parent role ID
            $parentRole = $this->roleModel->where('role_name', 'Orang Tua')->first();

            // Start transaction
            $this->db->transStart();

            // Process each row (skip header)
            for ($row = 2; $row <= $highestRow; $row++) {
                $this->results['total_rows']++;

                try {
                    $rowData = $this->extractRowData($worksheet, $row);

                    // Skip empty rows
                    if ($this->isEmptyRow($rowData)) {
                        $this->results['total_rows']--;
                        continue;
                    }

                    // Validate row data
                    $validation = $this->validateRowData($rowData, $row);
                    if (!$validation['valid']) {
                        $this->results['failed']++;
                        $this->results['errors'][] = "Baris {$row}: " . implode(', ', $validation['errors']);
                        continue;
                    }

                    // Process import for this row
                    $this->processStudentImport($rowData, $studentRole['id'], $parentRole ? $parentRole['id'] : null, $row);

                    $this->results['success']++;
                } catch (\Exception $e) {
                    $this->results['failed']++;
                    $this->results['errors'][] = "Baris {$row}: " . $e->getMessage();
                }
            }

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Terjadi kesalahan saat menyimpan data ke database.');
            }

            return $this->results;
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    /**
     * Validate Excel headers
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @return bool
     */
    protected function validateHeaders($worksheet)
    {
        $expectedHeaders = [
            'A1' => 'NISN',
            'B1' => 'NIS',
            'C1' => 'Nama Lengkap',
            'D1' => 'Email',
            'E1' => 'Password',
            'F1' => 'Jenis Kelamin',
            'G1' => 'Tempat Lahir',
            'H1' => 'Tanggal Lahir',
            'I1' => 'Agama',
            'J1' => 'Alamat',
            'K1' => 'Kelas',
            'L1' => 'Tanggal Masuk',
            'M1' => 'Status',
            'N1' => 'Nama Orang Tua',
            'O1' => 'Email Orang Tua',
        ];

        foreach ($expectedHeaders as $cell => $expectedValue) {
            $actualValue = trim($worksheet->getCell($cell)->getValue());
            if (strcasecmp($actualValue, $expectedValue) !== 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extract data from row
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @param int $row
     * @return array
     */
    protected function extractRowData($worksheet, $row)
    {
        return [
            'nisn' => trim($worksheet->getCell("A{$row}")->getValue()),
            'nis' => trim($worksheet->getCell("B{$row}")->getValue()),
            'full_name' => trim($worksheet->getCell("C{$row}")->getValue()),
            'email' => trim($worksheet->getCell("D{$row}")->getValue()),
            'password' => trim($worksheet->getCell("E{$row}")->getValue()),
            'gender' => strtoupper(trim($worksheet->getCell("F{$row}")->getValue())),
            'birth_place' => trim($worksheet->getCell("G{$row}")->getValue()),
            'birth_date' => $this->parseDate($worksheet->getCell("H{$row}")->getValue()),
            'religion' => trim($worksheet->getCell("I{$row}")->getValue()),
            'address' => trim($worksheet->getCell("J{$row}")->getValue()),
            'class_name' => trim($worksheet->getCell("K{$row}")->getValue()),
            'admission_date' => $this->parseDate($worksheet->getCell("L{$row}")->getValue()),
            'status' => trim($worksheet->getCell("M{$row}")->getValue()) ?: 'Aktif',
            'parent_name' => trim($worksheet->getCell("N{$row}")->getValue()),
            'parent_email' => trim($worksheet->getCell("O{$row}")->getValue()),
        ];
    }

    /**
     * Parse date from Excel
     * 
     * @param mixed $value
     * @return string|null
     */
    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // If numeric (Excel date serial)
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $date->format('Y-m-d');
        }

        // If string, try to parse
        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Check if row is empty
     * 
     * @param array $rowData
     * @return bool
     */
    protected function isEmptyRow($rowData)
    {
        return empty($rowData['nisn']) && empty($rowData['nis']) && empty($rowData['full_name']);
    }

    /**
     * Validate row data
     * 
     * @param array $rowData
     * @param int $rowNumber
     * @return array
     */
    protected function validateRowData($rowData, $rowNumber)
    {
        $errors = [];

        // Required fields
        if (empty($rowData['nisn'])) {
            $errors[] = 'NISN tidak boleh kosong';
        } elseif (strlen($rowData['nisn']) < 10 || !is_numeric($rowData['nisn'])) {
            $errors[] = 'NISN minimal 10 digit angka';
        }

        if (empty($rowData['nis'])) {
            $errors[] = 'NIS tidak boleh kosong';
        } elseif (strlen($rowData['nis']) < 5) {
            $errors[] = 'NIS minimal 5 karakter';
        }

        if (empty($rowData['full_name'])) {
            $errors[] = 'Nama lengkap tidak boleh kosong';
        }

        if (empty($rowData['email'])) {
            $errors[] = 'Email tidak boleh kosong';
        } elseif (!filter_var($rowData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }

        // Gender validation
        if (!in_array($rowData['gender'], ['L', 'P'])) {
            $errors[] = 'Jenis kelamin harus L atau P';
        }

        // Status validation
        $validStatus = ['Aktif', 'Alumni', 'Pindah', 'Keluar'];
        if (!in_array($rowData['status'], $validStatus)) {
            $errors[] = 'Status harus salah satu dari: ' . implode(', ', $validStatus);
        }

        // Check duplicate NISN
        $existingNisn = $this->studentModel->where('nisn', $rowData['nisn'])->first();
        if ($existingNisn) {
            $errors[] = "NISN {$rowData['nisn']} sudah terdaftar";
        }

        // Check duplicate NIS
        $existingNis = $this->studentModel->where('nis', $rowData['nis'])->first();
        if ($existingNis) {
            $errors[] = "NIS {$rowData['nis']} sudah terdaftar";
        }

        // Check duplicate email
        $existingEmail = $this->userModel->where('email', $rowData['email'])->first();
        if ($existingEmail) {
            $errors[] = "Email {$rowData['email']} sudah terdaftar";
        }

        // Validate class if provided
        if (!empty($rowData['class_name'])) {
            $class = $this->classModel->where('class_name', $rowData['class_name'])->first();
            if (!$class) {
                $errors[] = "Kelas '{$rowData['class_name']}' tidak ditemukan";
            }
        }

        // Validate parent email if provided
        if (!empty($rowData['parent_email']) && !filter_var($rowData['parent_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email orang tua tidak valid';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Process student import
     * 
     * @param array $rowData
     * @param int $studentRoleId
     * @param int|null $parentRoleId
     * @param int $rowNumber
     * @return void
     */
    protected function processStudentImport($rowData, $studentRoleId, $parentRoleId, $rowNumber)
    {
        // Get class ID if class name provided
        $classId = null;
        if (!empty($rowData['class_name'])) {
            $class = $this->classModel->where('class_name', $rowData['class_name'])->first();
            $classId = $class ? $class['id'] : null;
        }

        // Generate username from NISN
        $username = $rowData['nisn'];

        // Use provided password or generate default
        $password = !empty($rowData['password']) ? $rowData['password'] : 'password123';

        // Create user account for student
        $userData = [
            'role_id' => $studentRoleId,
            'username' => $username,
            'email' => $rowData['email'],
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $rowData['full_name'],
            'is_active' => 1,
        ];

        if (!$this->userModel->insert($userData)) {
            throw new \Exception('Gagal membuat akun user: ' . implode(', ', $this->userModel->errors()));
        }

        $userId = $this->userModel->getInsertID();

        // Create parent account if parent info provided
        $parentId = null;
        if (!empty($rowData['parent_name']) && !empty($rowData['parent_email'])) {
            // Check if parent email already exists
            $existingParent = $this->userModel->where('email', $rowData['parent_email'])->first();

            if ($existingParent) {
                $parentId = $existingParent['id'];
                $this->results['warnings'][] = "Baris {$rowNumber}: Email orang tua sudah terdaftar, menggunakan akun yang ada.";
            } else {
                // Create new parent account
                $parentData = [
                    'role_id' => $parentRoleId,
                    'username' => strtolower(str_replace(' ', '_', $rowData['parent_name'])) . '_' . substr($rowData['nisn'], -4),
                    'email' => $rowData['parent_email'],
                    'password' => password_hash('parent123', PASSWORD_DEFAULT),
                    'full_name' => $rowData['parent_name'],
                    'is_active' => 1,
                ];

                if ($this->userModel->insert($parentData)) {
                    $parentId = $this->userModel->getInsertID();
                }
            }
        }

        // Create student record
        $studentData = [
            'user_id' => $userId,
            'class_id' => $classId,
            'nisn' => $rowData['nisn'],
            'nis' => $rowData['nis'],
            'gender' => $rowData['gender'],
            'birth_place' => $rowData['birth_place'] ?: null,
            'birth_date' => $rowData['birth_date'],
            'religion' => $rowData['religion'] ?: null,
            'address' => $rowData['address'] ?: null,
            'parent_id' => $parentId,
            'admission_date' => $rowData['admission_date'],
            'status' => $rowData['status'],
            'total_violation_points' => 0,
        ];

        if (!$this->studentModel->insert($studentData)) {
            // Rollback user if student creation fails
            $this->userModel->delete($userId);
            throw new \Exception('Gagal membuat data siswa: ' . implode(', ', $this->studentModel->errors()));
        }
    }

    /**
     * Generate Excel template for student import
     * 
     * @param string $savePath Path to save template
     * @return string Path to generated file
     */
    public function generateTemplate($savePath = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'NISN',
            'NIS',
            'Nama Lengkap',
            'Email',
            'Password',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Alamat',
            'Kelas',
            'Tanggal Masuk',
            'Status',
            'Nama Orang Tua',
            'Email Orang Tua',
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            '1234567890',
            'NIS001',
            'Ahmad Fauzi',
            'ahmad.fauzi@example.com',
            'password123',
            'L',
            'Bandung',
            '2008-05-15',
            'Islam',
            'Jl. Contoh No. 123',
            'X-IPA-1',
            '2024-07-01',
            'Aktif',
            'Bapak Ahmad',
            'bapak.ahmad@example.com',
        ];
        $sheet->fromArray([$sampleData], null, 'A2');

        // Set column widths
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add notes
        $sheet->getCell('A4')->setValue('PETUNJUK:');
        $sheet->getCell('A5')->setValue('1. NISN: Nomor Induk Siswa Nasional (minimal 10 digit angka)');
        $sheet->getCell('A6')->setValue('2. Jenis Kelamin: L untuk Laki-laki, P untuk Perempuan');
        $sheet->getCell('A7')->setValue('3. Tanggal: Format YYYY-MM-DD atau DD/MM/YYYY');
        $sheet->getCell('A8')->setValue('4. Status: Aktif, Alumni, Pindah, atau Keluar');
        $sheet->getCell('A9')->setValue('5. Password: Kosongkan untuk menggunakan password default (password123)');
        $sheet->getCell('A10')->setValue('6. Kelas: Harus sesuai dengan nama kelas yang sudah terdaftar');

        // Save file
        if (!$savePath) {
            $savePath = WRITEPATH . 'uploads/template_import_siswa.xlsx';
        }

        // Ensure directory exists
        $directory = dirname($savePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($savePath);

        return $savePath;
    }

    /**
     * Reset results
     * 
     * @return void
     */
    protected function resetResults()
    {
        $this->results = [
            'total_rows' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'warnings' => [],
        ];
    }

    /**
     * Get import results
     * 
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }
}
