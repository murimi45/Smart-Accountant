<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required
               value="{{ old('name', $grade->name ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Basic pay (KES) <span class="text-danger">*</span></label>
        <input type="number" name="basic_pay" class="form-control" required min="0" step="0.01"
               value="{{ old('basic_pay', $grade->basic_pay ?? '') }}">
    </div>
</div>

<label class="form-label">Standing allowances</label>
@php
    $rows = $grade
        ? $grade->allowances->map(fn ($a) => ['name' => $a->name, 'amount' => $a->amount])->all()
        : [];
    $rows = array_pad($rows, max(count($rows) + 1, 2), ['name' => '', 'amount' => '']);
@endphp
@foreach($rows as $i => $row)
    <div class="row g-2 mb-2">
        <div class="col-md-7">
            <input type="text" name="allowances[{{ $i }}][name]" class="form-control" placeholder="Allowance name" value="{{ $row['name'] }}">
        </div>
        <div class="col-md-5">
            <input type="number" name="allowances[{{ $i }}][amount]" class="form-control" placeholder="Amount" min="0" step="0.01" value="{{ $row['amount'] }}">
        </div>
    </div>
@endforeach
<p class="text-muted small mb-0">Leave a row blank to ignore it. Saving replaces the allowances on this grade.</p>