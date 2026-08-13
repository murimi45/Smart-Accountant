<div>
    @if($message)<div class="alert alert-success">{{ $message }}</div>@endif
    @if($error)<div class="alert alert-danger">{{ $error }}</div>@endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        @if($competency)
                            <th>Band (EE/ME/AE/BE)</th>
                            <th>Comment</th>
                        @else
                            <th>Score @if($assessment->max_score)/ {{ $assessment->max_score }}@endif</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                        <tr>
                            <td>{{ $row['student_name'] }}</td>
                            @if($competency)
                                <td>
                                    <select class="form-select form-select-sm" wire:model="rows.{{ $i }}.band_code">
                                        <option value="">—</option>
                                        @foreach(['EE','ME','AE','BE'] as $band)
                                            <option value="{{ $band }}">{{ $band }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input class="form-control form-control-sm" wire:model="rows.{{ $i }}.comment"></td>
                            @else
                                <td><input type="number" step="0.01" class="form-control form-control-sm" wire:model="rows.{{ $i }}.raw_score"></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" wire:click="save" @disabled(!$assessment->isOpen())>Save marks</button>
            @unless($assessment->isOpen())
                <span class="text-muted ms-2">Assessment must be open to enter marks.</span>
            @endunless
        </div>
    </div>
</div>
