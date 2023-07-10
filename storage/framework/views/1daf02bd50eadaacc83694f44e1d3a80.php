<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Age</th>
            <th>Position</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $barangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $barang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($barang->firstname); ?></td>
                <td><?php echo e($barang->lastname); ?></td>
                <td><?php echo e($barang->email); ?></td>
                <td><?php echo e($barang->age); ?></td>
                <td><?php echo e($barang->satuan->name); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH C:\Users\fauzan\masterBarangUts\resources\views/barang/export_excel.blade.php ENDPATH**/ ?>