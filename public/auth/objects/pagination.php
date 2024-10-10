<?php

function renderPagination($currentPage, $totalPages) {
    ?>
    <div class="row">
        <div class="col text-center">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    
                    <!-- Previous Button -->
                    <li class="page-item <?php if ($currentPage == 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage > 1 ? $currentPage - 1 : '#'; ?>" 
                           aria-label="Previous" aria-disabled="<?php echo $currentPage == 1 ? 'true' : 'false'; ?>">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Page Number Buttons -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php if ($i == $currentPage) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <li class="page-item <?php if ($currentPage == $totalPages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage < $totalPages ? $currentPage + 1 : '#'; ?>" 
                           aria-label="Next" aria-disabled="<?php echo $currentPage == $totalPages ? 'true' : 'false'; ?>">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
    <?php
}
?>
