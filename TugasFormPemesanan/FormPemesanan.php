<?php
// ----------------- Konfigurasi URL Form (untuk tombol "Kembali ke Form") -----------------
$BASE_FORM_URL = "http://localhost/PBL/TugasFormPemesanan/FormPemesanan.php";
// -----------------------------------------------------------------------------------------

$isReceiptOnly = isset($_GET['order']);
$receiptOrderId = $isReceiptOnly ? htmlspecialchars($_GET['order']) : null;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= $isReceiptOnly ? "Struk $receiptOrderId" : "Form Pemesanan Makanan Online" ?></title>

  <!-- Bootstrap 5 (CSS & JS BUNDLE) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- jsPDF untuk unduh PDF -->
  <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

  <style>
    :root { --soft:#f7f9fb; --bd:#e5e7eb; --ink:#0f172a; }
    body{background:var(--soft); color:var(--ink);}
    .card-shadow{box-shadow:0 6px 18px rgba(0,0,0,.06);}
    .mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace}
    .receipt-pre{white-space:pre-wrap; line-height:1.4}
    .badge-menu{background:#eef2ff; color:#3730a3; font-weight:600}
    .qty-input{max-width:110px}
    .table-clean thead th{
    background:#f8f9fa;   
    font-weight:600;
    }
    .table-clean td,.table-clean th{
    padding:.9rem 1.1rem;     
    }
    .table-clean tbody tr + tr td{
    border-top:1px solid #edf1f5;
    }
  </style>
</head>
<body>

<?php if ($isReceiptOnly): ?>
<!-- =========================== HALAMAN STRUK SAJA =========================== -->
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Struk Pesanan</h1>
    <div class="d-flex gap-2">
      <button id="btnDownloadAlone" class="btn btn-success btn-sm">Download Struk (PDF)</button>
      <a href="<?= $BASE_FORM_URL ?>" class="btn btn-outline-secondary btn-sm">Kembali ke Form</a>
    </div>
  </div>

  <div class="card card-shadow">
    <div class="card-body">
      <pre id="receiptTextOnly" class="mono receipt-pre mb-0">Memuat struk...</pre>
    </div>
  </div>
</div>

<script>
  // ---------- Utilitas ----------
  const rupiah = n => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(n).replace(",00","");
  const loadOrders = () => { try{return JSON.parse(localStorage.getItem('orders')||'[]')}catch(e){return[]} };
  function formatReceipt(o){
    if(!o) return "Data tidak ditemukan.";
    let s=[];
    s.push("=== DETAIL PESANAN ===");
    s.push(`No Pesanan : ${o.id}`);
    s.push(`Nama       : ${o.name}`);
    s.push(`No HP      : ${o.phone}`);
    s.push(`Alamat     : ${o.address}`);
    s.push("\nPESANAN:");
    o.items.forEach(i=>{
      s.push(`- ${i.name}`);
      s.push(`${i.qty} x ${rupiah(i.price)} = ${rupiah(i.subtotal)}`);
    });
    s.push("------------------------");
    s.push(`TOTAL ${rupiah(o.total)}`);
    if(o.notes?.trim()){ s.push("\nCatatan: "+o.notes); }
    s.push("\nTanggal: "+o.createdAtDisplay);
    return s.join("\n");
  }

  const orderId = new URLSearchParams(location.search).get('order');
  const order = loadOrders().find(x=>x.id===orderId);
  const receiptTxt = formatReceipt(order);
  document.getElementById('receiptTextOnly').textContent = receiptTxt;

  document.getElementById('btnDownloadAlone').addEventListener('click', ()=>{
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({unit:'pt', format:'a4'});
    doc.setFont('courier','normal'); doc.setFontSize(12);
    const margin=40, width=515;
    doc.text(doc.splitTextToSize(receiptTxt,width), margin, margin);
    doc.save(`${order?.id||'STRUK'}.pdf`);
  });
</script>
</body></html>
<?php exit; endif; ?>

<!-- =========================== HALAMAN FORM + RIWAYAT =========================== -->
<div class="container py-4">
  <h1 class="h4 mb-4">Form Pemesanan Makanan Online</h1>

  <div class="row g-4">
    <div class="col-12 col-lg-7">
      <div class="card card-shadow">
        <div class="card-body">
          <div class="mb-3">
            <div class="fw-bold mb-2">Data Pemesan</div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nama Pemesan</label>
                <input type="text" id="name" class="form-control" placeholder="Nama lengkap">
              </div>
              <div class="col-md-6">
                <label class="form-label">No HP</label>
                <input type="tel" id="phone" class="form-control" placeholder="08xxxxxxxxxx">
              </div>
              <div class="col-12">
                <label class="form-label">Alamat Pengiriman</label>
                <textarea id="address" rows="3" class="form-control" placeholder="Jl. Nama Jalan No. xx Kota"></textarea>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <div class="fw-bold mb-2">Menu Makanan</div>
            <div id="menuList" class="vstack gap-2"></div>
            <div class="border-top pt-3 mt-3 d-flex justify-content-between">
              <span class="text-muted">Subtotal</span>
              <strong id="subTotalLabel">Rp 0</strong>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Catatan (opsional)</label>
            <textarea id="notes" rows="3" class="form-control" placeholder="Contoh: pedas, kurang asin, dsb."></textarea>
          </div>

          <div class="d-flex gap-2">
            <button id="btnSubmit" class="btn btn-primary">Pesan Sekarang</button>
            <button id="btnReset"  class="btn btn-outline-secondary">Reset</button>
          </div>
        </div>
      </div>

      <div id="receiptCard" class="card card-shadow mt-4 d-none">
        <div class="card-body">
          <pre id="receiptText" class="mono receipt-pre mb-3">_</pre>
          <div class="d-flex flex-wrap gap-2">
            <button id="btnDownload" class="btn btn-success">Download Struk (PDF)</button>
            <a href="#history" class="btn btn-info text-white">Lihat Riwayat Pesanan</a>
            <button id="btnNewOrder" class="btn btn-outline-primary">Pesan Lagi</button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-5" id="history">
        <div class="card card-shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-bold">Riwayat Pesanan</span>
            <button id="btnClear" class="btn btn-sm btn-outline-danger">Bersihkan Riwayat</button>
            </div>

            <!-- beri padding card-body agar tabel tidak mepet -->
            <div class="card-body p-3">
            <!-- border + rounded agar seperti contoh dan clean -->
            <div class="table-responsive rounded-3 border">
                <table class="table table-striped align-middle mb-0 table-clean">
                <thead>
                    <tr>
                    <th>No Pesanan</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th>Total</th>
                    <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="historyBody">
                    <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Belum ada pesanan.
                    </td>
                    </tr>
                </tbody>
                </table>
            </div>
            </div>

            <div class="card-footer bg-white">
            <a href="<?= $BASE_FORM_URL ?>" class="btn btn-primary">Pesan Baru</a>
            </div>
        </div>
    </div>

  </div>
</div>

<script>
  // ================== DATA MENU ==================
  const MENUS = [
    { id:'nasi',    name:'Nasi Goreng', price:15000 },
    { id:'mie',     name:'Mie Goreng',  price:12000 },
    { id:'ayam',    name:'Ayam Bakar',  price:20000 },
    { id:'esteh',   name:'Es Teh',      price:3000  },
    { id:'esjeruk', name:'Es Jeruk',    price:4000  }
  ];

  // ================== UTILITAS ==================
  const byId = id => document.getElementById(id);
  const rupiah = n => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(n).replace(",00","");
  const loadOrders = () => { try{return JSON.parse(localStorage.getItem('orders')||'[]')}catch(e){return[]} };
  const saveOrders = list => localStorage.setItem('orders', JSON.stringify(list));
  const nowDisp = d => {
    const pad = n => String(n).padStart(2,"0");
    return `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
  };
  function nextOrderNo(){
    let seq = parseInt(localStorage.getItem('order_seq')||'0',10)+1;
    localStorage.setItem('order_seq', String(seq).padStart(3,'0'));
    return `ORDER-${String(seq).padStart(3,'0')}`;
  }
  function formatReceipt(order){
    let s=[];
    s.push("=== DETAIL PESANAN ===");
    s.push(`No Pesanan : ${order.id}`);
    s.push(`Nama       : ${order.name}`);
    s.push(`No HP      : ${order.phone}`);
    s.push(`Alamat     : ${order.address}`);
    s.push("\nPESANAN:");
    order.items.forEach(i=>{
      s.push(`- ${i.name}`);
      s.push(`${i.qty} x ${rupiah(i.price)} = ${rupiah(i.subtotal)}`);
    });
    s.push("------------------------");
    s.push(`TOTAL ${rupiah(order.total)}`);
    if(order.notes?.trim()) s.push("\nCatatan: "+order.notes);
    s.push("\nTanggal: "+order.createdAtDisplay);
    return s.join("\n");
  }

  // ================== RENDER MENU ==================
  const menuList = byId('menuList');
  MENUS.forEach(m=>{
    const row = document.createElement('div');
    row.className = "d-flex align-items-center justify-content-between border rounded px-2 py-2";
    row.innerHTML = `
      <div class="form-check">
        <input class="form-check-input menu-check" type="checkbox" id="chk_${m.id}" data-id="${m.id}">
        <label class="form-check-label" for="chk_${m.id}">
          ${m.name} <span class="badge badge-menu ms-2">${rupiah(m.price)}</span>
        </label>
      </div>
      <div class="input-group input-group-sm qty-input">
        <button class="btn btn-outline-secondary minus" type="button">&minus;</button>
        <input type="number" class="form-control text-center qty" min="0" value="0" data-id="${m.id}">
        <button class="btn btn-outline-secondary plus" type="button">&plus;</button>
      </div>
    `;
    menuList.appendChild(row);
  });

  // ====== INTI PERBAIKAN QTY & SUBTOTAL ======
  function getQtyInput(id){ return document.querySelector(`.qty[data-id="${id}"]`); }
  function getChk(id){ return byId(`chk_${id}`); }

  function syncQtyAndCheck(id, qty){
    // batas bawah 0
    if(qty < 0) qty = 0;
    // set nilai
    getQtyInput(id).value = qty;
    // auto centang jika qty > 0, hilangkan jika 0
    getChk(id).checked = qty > 0;
  }

  function collectItems(){
    const items=[];
    MENUS.forEach(m=>{
      const qty = parseInt(getQtyInput(m.id).value||'0',10);
      if(qty>0){
        items.push({id:m.id, name:m.name, price:m.price, qty, subtotal: m.price*qty});
      }
    });
    return items;
  }

  function updateSubtotal(){
    const items = collectItems();
    const total = items.reduce((a,b)=>a+b.subtotal,0);
    byId('subTotalLabel').textContent = rupiah(total);
  }

  // Delegate: klik + / -
  menuList.addEventListener('click', (e)=>{
    const isPlus = e.target.classList.contains('plus');
    const isMinus = e.target.classList.contains('minus');
    if(!(isPlus||isMinus)) return;

    const input = e.target.parentElement.querySelector('.qty');
    const id = input.dataset.id;
    let qty = parseInt(input.value||'0',10);

    qty = isPlus ? qty+1 : qty-1;
    if(qty < 0) qty = 0;

    syncQtyAndCheck(id, qty);
    updateSubtotal();
  });

  // Input manual qty
  menuList.addEventListener('input', (e)=>{
    if(!e.target.classList.contains('qty')) return;
    const id = e.target.dataset.id;
    let v = parseInt(e.target.value||'0',10);
    if(isNaN(v) || v < 0) v = 0;
    syncQtyAndCheck(id, v);
    updateSubtotal();
  });

  // Klik checkbox menu
  menuList.addEventListener('change', (e)=>{
    if(!e.target.classList.contains('menu-check')) return;
    const id = e.target.dataset.id;
    const input = getQtyInput(id);
    let v = parseInt(input.value||'0',10);
    if(e.target.checked && v===0) v = 1; // auto 1 saat dicentang
    if(!e.target.checked) v = 0;
    syncQtyAndCheck(id, v);
    updateSubtotal();
  });

  // ================== SUBMIT / STRUK / RIWAYAT ==================
  function renderHistory(){
    const body = byId('historyBody');
    const list = loadOrders();
    if(list.length===0){
      body.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>`;
      return;
    }
    body.innerHTML = list.map(o=>`
      <tr>
        <td class="fw-semibold">${o.id}</td>
        <td>${o.createdAtDisplay}</td>
        <td>${o.name}</td>
        <td>${o.phone}</td>
        <td>${rupiah(o.total)}</td>
        <td class="text-center">
          <a class="btn btn-sm btn-info text-white" target="_blank" href="?order=${encodeURIComponent(o.id)}">Lihat Detail</a>
        </td>
      </tr>
    `).join('');
  }

  function downloadReceiptFromCard(){
    const text = byId('receiptText').textContent.trim();
    if(!text) return;
    const idLine = text.split("\n").find(l=>l.startsWith("No Pesanan"));
    const file = idLine ? idLine.split(":")[1].trim() : "STRUK";
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({unit:'pt', format:'a4'});
    doc.setFont('courier','normal'); doc.setFontSize(12);
    const margin=40, width=515;
    doc.text(doc.splitTextToSize(text,width), margin, margin);
    doc.save(`${file}.pdf`);
  }

  byId('btnSubmit').addEventListener('click', ()=>{
    const name=byId('name').value.trim(), phone=byId('phone').value.trim(),
          address=byId('address').value.trim(), notes=byId('notes').value.trim();

    if(!name||!phone||!address){ alert("Nama, No HP, dan Alamat wajib diisi."); return; }

    const items = collectItems();
    if(items.length===0){ alert("Pilih minimal 1 menu."); return; }

    const total = items.reduce((a,b)=>a+b.subtotal,0);
    const order = {
      id: nextOrderNo(),
      name, phone, address, notes,
      items, total,
      createdAt: new Date().toISOString(),
      createdAtDisplay: nowDisp(new Date())
    };

    const all = loadOrders(); all.unshift(order); saveOrders(all);

    byId('receiptText').textContent = formatReceipt(order);
    byId('receiptCard').classList.remove('d-none');
    renderHistory();
  });

  byId('btnNewOrder').addEventListener('click', ()=>{
    ['name','phone','address','notes'].forEach(id=>byId(id).value="");
    document.querySelectorAll('.qty').forEach(q=>q.value=0);
    document.querySelectorAll('.menu-check').forEach(c=>c.checked=false);
    updateSubtotal();
    window.scrollTo({top:0,behavior:'smooth'});
  });

  byId('btnReset').addEventListener('click', ()=>{
    document.querySelectorAll('.qty').forEach(q=>q.value=0);
    document.querySelectorAll('.menu-check').forEach(c=>c.checked=false);
    updateSubtotal();
  });

  byId('btnDownload').addEventListener('click', downloadReceiptFromCard);
  byId('btnClear').addEventListener('click', ()=>{
    if(confirm('Hapus semua riwayat pesanan?')){
      localStorage.removeItem('orders');
      renderHistory();
    }
  });

  // init
  renderHistory();
  updateSubtotal();
</script>
</body>
</html>
