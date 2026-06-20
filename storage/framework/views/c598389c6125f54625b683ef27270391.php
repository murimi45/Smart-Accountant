<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Statement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .student-info {
            margin-bottom: 20px;
        }
        h3 {
            margin-top: 25px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            text-align: left;
        }
        th, td {
            padding: 8px;
        }
        .summary {
            margin-top: 30px;
            width: 50%;
            float: right;
        }
        .summary td {
            padding: 6px 10px;
        }
        .balance {
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Student Invoice Statement</h2>
        <p><strong>School Accounting System</strong></p>
    </div>

    <div class="student-info">
        <strong>Student:</strong> <?php echo e($student->name); ?><br>
        <strong>Class:</strong> <?php echo e($student->class->name ?? 'N/A'); ?><br>
        <strong>Term:</strong> <?php echo e($invoices->first()->term->name ?? 'N/A'); ?>

    </div>

    <?php
        $grandTotal = 0;
        $grandPaid = 0;
    ?>

    <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="invoice-info" style="margin-bottom: 10px;">
            <strong>Invoice #:</strong> <?php echo e($invoice->id); ?><br>
            <strong>Date Issued:</strong> <?php echo e($invoice->created_at->format('d M Y')); ?>

        </div>

        <h3>Invoice Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="width: 100px; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item->description); ?></td>
                        <td style="text-align:right;"><?php echo e(number_format($item->amount, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <h3>Payments Made</h3>
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Date</th>
                    <th style="width: 100px; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $invoice->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(ucfirst($payment->method)); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($payment->payment_date)->format('d M Y')); ?></td>
                        <td style="text-align:right;"><?php echo e(number_format($payment->amount, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">No payments recorded</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php
            $grandTotal += $invoice->items->sum('amount');
            $grandPaid += $invoice->payments->sum('amount');
        ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <div class="summary">
        <table>
            <tr>
                <td><strong>Total Invoiced:</strong></td>
                <td style="text-align:right;"><?php echo e(number_format($grandTotal, 2)); ?></td>
            </tr>
            <tr>
                <td><strong>Total Paid:</strong></td>
                <td style="text-align:right;"><?php echo e(number_format($grandPaid, 2)); ?></td>
            </tr>
            <tr class="balance">
                <td><strong>Balance:</strong></td>
                <td style="text-align:right;"><?php echo e(number_format($grandTotal - $grandPaid, 2)); ?></td>
            </tr>
        </table>
    </div>

</body>
</html>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/statements/single.blade.php ENDPATH**/ ?>