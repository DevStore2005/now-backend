<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item {!! $data['currentPage'] == 1 ? "disabled" : ""!!}">
      <a class="page-link" href="{!! $data['previousPage'] !!}">Previous Page</a>
    </li>
    <li class="page-item {!! $data['hasMore'] ? "" : "disabled" !!}">
      <a class="page-link" href="{!! $data['nextPage'] !!}">Next Page</a>
    </li>
  </ul>
</nav>