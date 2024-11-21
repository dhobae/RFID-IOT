<?php

session_start();

// Redirect ke halaman login jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Access Logs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- data table -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

    <style>
        .white-text th {
            color: #ffff;
        }

        #logTable td:nth-child(1) {
            text-align: center !important;
        }

        #logTable td:nth-child(2) {
            text-align: left !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header pb-0">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <p class="mb-0"><strong>Your IP Address:</strong> <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
                    <p class="mb-0"><strong>Welcome Admin!</strong></p>
                </div>
            </div>
            <div class="card-body">


                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>RFID Access Logs</h2>
                    </div>
                    <div>
                        <a href="<?= 'auth/logout.php' ?>" class="btn btn-sm btn-danger">Logout</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="tabel-rfid">
                        <thead class="bg-info">
                            <tr class="white-text">
                                <th class="text-center">NO</th>
                                <!-- <th>ID</th> -->
                                <th class="text-left">RFID ID</th>
                                <th>Name</th>
                                <th>Entry Time</th>
                                <th>Exit Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="logTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>


    </script>
    <!-- <script>
        $(document).ready(function() {
            function loadLogs() {
                $.ajax({
                    url: 'api/rfid_api.php',
                    method: 'GET',
                    success: function(data) {
                        let tableContent = '';
                        data.forEach(log => {
                            tableContent += `<tr>
                                <td>${log.id}</td>
                                <td>${log.rfid_id}</td>
                                <td>${log.name}</td>
                                <td>${log.entry_time || '-'}</td>
                                <td>${log.exit_time || '-'}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm deleteBtn" data-id="${log.id}">Delete</button>
                                </td>
                            </tr>`;
                        });
                        $('#logTable').html(tableContent);
                    }
                });
            }

            loadLogs();
            setInterval(loadLogs, 5000);

            $(document).on('click', '.deleteBtn', function() {
                const logId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This log will be deleted permanently.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/rfid_api.php/delete',
                            method: 'DELETE',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                id: logId
                            }),
                            success: function(response) {
                                if (response.message) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    loadLogs();
                                } else {
                                    Swal.fire('Error!', 'Failed to delete log.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'An error occurred while deleting log.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script> -->

    <script>
        $(document).ready(function() {
            // API Key Anda
            const apiKey = '4ea9d17a5e53b5ae54f53484';

            // Variabel untuk menyimpan instance DataTable
            let table;

            // Fungsi untuk memuat log
            function loadLogs() {
                $.ajax({
                    url: 'api/rfid_api.php',
                    method: 'GET',
                    headers: {
                        'API-Key': apiKey // Tambahkan API key pada header API-Key
                    },
                    success: function(data) {
                        let no = 1; // Inisialisasi nomor urut
                        let tableContent = [];

                        // Membuat data array untuk DataTable
                        data.forEach(log => {
                            tableContent.push([
                                no++,
                                log.rfid_id,
                                log.name,
                                log.entry_time || '-',
                                log.exit_time || '-',
                                `<button class="btn btn-danger btn-sm deleteBtn" data-id="${log.id}">Delete</button>`
                            ]);
                        });

                        // Jika DataTable sudah diinisialisasi
                        if (table) {
                            table.clear(); // Bersihkan data lama
                            table.rows.add(tableContent); // Tambahkan data baru
                            table.draw(); // Render ulang tabel
                        } else {
                            // Inisialisasi DataTable jika belum ada
                            table = $('#tabel-rfid').DataTable({
                                data: tableContent,
                                columns: [{
                                        title: "No"
                                    },
                                    {
                                        title: "RFID ID"
                                    },
                                    {
                                        title: "Name"
                                    },
                                    {
                                        title: "Entry Time"
                                    },
                                    {
                                        title: "Exit Time"
                                    },
                                    {
                                        title: "Actions"
                                    }
                                ],
                                columnDefs: [{
                                    targets: 5,
                                    orderable: false
                                }]
                            });
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan saat memuat data log.', 'error');
                    }
                });
            }


            // Panggil loadLogs untuk pertama kali
            loadLogs();

            // Update data setiap 5 detik
            setInterval(loadLogs, 5000);

            // Fungsi untuk menghapus log
            $(document).on('click', '.deleteBtn', function() {
                const logId = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Log ini akan dihapus secara permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Tidak, batal!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/rfid_api.php/delete',
                            method: 'DELETE',
                            headers: {
                                'API-Key': apiKey
                            },
                            contentType: 'application/json',
                            data: JSON.stringify({
                                id: logId
                            }),
                            success: function(response) {
                                if (response.message) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    loadLogs(); // Memuat ulang log setelah penghapusan
                                } else {
                                    Swal.fire('Error!', 'Gagal menghapus log.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus log.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

    <!-- <td>${log.id}</td> -->

</body>

</html>