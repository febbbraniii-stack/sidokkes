document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-confirm]").forEach(function (link) {

        // Menambahkan konfirmasi ketika elemen diklik
        link.addEventListener("click", function (event) {

            // Menampilkan pesan konfirmasi
            const pesan =
                link.getAttribute("data-confirm") ||
                "Lanjutkan tindakan ini?";

            const yakin = window.confirm(pesan);

            // Jika pengguna memilih Batal, tindakan dibatalkan
            if (!yakin) {
                event.preventDefault();
            }

        });
    });

});