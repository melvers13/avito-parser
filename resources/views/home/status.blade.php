<samp>
    <ul class="list mb-0">
        @foreach ($events as $event)
            <li>{{ $event['message'] }}</li>
        @endforeach
    </ul>
</samp>
