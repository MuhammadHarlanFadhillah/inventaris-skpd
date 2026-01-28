</div> 
<footer class="bg-white text-dark mt-auto py-4 border-top">
  <div class="container">
    <div class="row align-items-center">
      
      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <small class="fw-bold text-primary">Sistem Inventaris Barang (SIMBAR)</small><br>
        <small class="text-muted">&copy; <?php echo date('Y'); ?> IF-3 2023 UNIKOM</small>
      </div>

      <div class="col-md-6 text-center text-md-end">
        <small class="text-muted">Dikembangkan oleh:</small><br>
        <span class="fw-bold small">IF-3 UNIKOM</span>
      </div>

    </div>
  </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        // Inisialisasi DataTables pada class .table
        $('.table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            // Opsi tambahan agar tabel lebih responsif
            autoWidth: false,
            responsive: true
        });
    });
</script>

</body>
</html>