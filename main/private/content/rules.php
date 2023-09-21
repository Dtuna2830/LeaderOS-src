<section class="section page-section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kurallar</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            Kurallar
          </div>
          <div class="card-body">
            <?php echo str_replace("%servername%", $serverName, $readSettings["rules"]); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
