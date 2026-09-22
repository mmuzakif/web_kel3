let nama = prompt("Halaman ini menyatakan\n\nTuliskan nama Kamu");

if (nama !== null && nama !== "") {
    document.getElementById("hasil").innerHTML =
        "Nama saya " + nama +
        ", saya akan mengamalkan Pancasila dan UUD 1945 sebagai Dasar Negara.";

    document.getElementById("uud").style.display = "block";
}