pipeline {
    // Menjalankan pipeline pada agent mana pun yang tersedia di Jenkins
    agent any

    // Definisi variabel lingkungan agar skrip lebih rapi dan mudah diubah
    environment {
        SERVER_USER  = 'nfbsb'                             // Username SSH server target
        SERVER_HOST  = '192.168.100.125'                  // IP Address server target
        PROJECT_PATH = '/home/nfbsb/php/kantin_nfbs'     // Lokasi folder proyek di server
        GIT_REPO     = 'https://github.com/khoirulmustofa/kantin.git' // URL Repository
    }

    stages {
        // Tahap utama: Deployment menggunakan protokol SSH
        stage('Deploy via SSH') {
            steps {
                // Menggunakan plugin SSH Agent untuk otentikasi kunci (private key)
                sshagent (credentials: ['serverpaklukman']) {
                    sh """
                        # Membuka koneksi SSH dan menjalankan perintah di dalam tanda kutip tunggal
                        ssh -o StrictHostKeyChecking=no ${SERVER_USER}@${SERVER_HOST} '
                            
                            # === Langkah 1: Sinkronisasi Kode Git ===
                            # Jika folder proyek belum ada atau bukan repo git, lakukan clone baru
                            if [ ! -d "${PROJECT_PATH}" ] || [ ! -d "${PROJECT_PATH}/.git" ]; then
                                echo "Proyek tidak ditemukan. Melakukan clone segar..."
                                rm -rf "${PROJECT_PATH}"
                                git clone ${GIT_REPO} ${PROJECT_PATH}
                            else
                                # Jika sudah ada, tarik pembaruan terbaru dari branch main
                                echo "Proyek ditemukan. Menarik pembaruan terbaru..."
                                cd ${PROJECT_PATH}
                                git fetch origin
                                git reset --hard origin/main
                                git clean -fd
                            fi

                            # === Langkah 2: Pindah ke Direktori Kerja ===
                            cd "${PROJECT_PATH}"

                            # === Langkah 3: Manajemen Container (Stop) ===
                            # Menghentikan container yang sedang berjalan agar tidak bentrok saat build
                            echo "[INFO] Menghentikan container lama (jika ada)..."
                            if [ -f docker-compose.yml ]; then
                                docker compose down
                            else
                                echo "[WARN] docker-compose.yml tidak ada! Lewati langkah stop."
                            fi

                            # === Langkah 4: Build & Run Container ===
                            # Membangun ulang image (build) dan menjalankan container di background (-d)
                            echo "[INFO] Membangun dan menjalankan container baru..."
                            if [ -f docker-compose.yml ]; then
                                docker compose up -d --build
                            else
                                echo "[ERROR] File docker-compose.yml hilang! Gagalkan build."
                                exit 1
                            fi

                            # Memberikan jeda waktu agar layanan (database/app) siap sepenuhnya
                            sleep 3

                            # === Langkah 5: Pengaturan Hak Akses (Permissions) ===
                            # Menyesuaikan owner dan permission agar Laravel bisa menulis log/cache
                            echo "[INFO] Mengatur hak akses file di dalam container..."
                            
                            # Mengatur folder storage dan cache agar bisa ditulis oleh server (www-data)
                            docker exec kantin_nfbs chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
                            docker exec kantin_nfbs chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
                            
                            # Mengatur folder upload publik
                            docker exec kantin_nfbs chown -R www-data:www-data /var/www/html/public/uploads
                            docker exec kantin_nfbs chmod -R 775 /var/www/html/public/uploads
                            docker exec kantin_nfbs chmod g+s /var/www/html/public/uploads

                            # === Langkah 6: Symlink Storage ===
                            # Menghubungkan folder storage ke folder public agar file upload bisa diakses web
                            docker exec kantin_nfbs php artisan storage:link

                            echo "[SUCCESS] Deployment, build, dan pengaturan izin berhasil diselesaikan!"
                        '
                    """
                }
            }
        }
    }
}