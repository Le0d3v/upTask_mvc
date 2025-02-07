<div class="breadcrumb-container">
  <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
          <?php foreach ($breadcrumbs as $breadcrumb): ?>
              <li class="breadcrumb-item">
                  <a class="breadcrumb-enlace" href="<?php echo $breadcrumb['url']; ?>"><?php echo $breadcrumb['label']; ?></a>
              </li>
          <?php endforeach; ?>
      </ol>
  </nav>
</div>