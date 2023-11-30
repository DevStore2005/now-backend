<table class="display table table-striped table-hover">
    <thead>
        <tr>
            @foreach ($head as $key)
                <th>{!!$key!!}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($body as $entity)
        <tr>
            @foreach($entity as $key => $value)
                <td>{!!$value!!}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>