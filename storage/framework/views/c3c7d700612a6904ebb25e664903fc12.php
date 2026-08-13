
<?php if (\Illuminate\Support\Facades\Blade::check('module', 'grading')): ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin', 'teacher')): ?>
    <div class="sidebar_blog_2">
        <h4>Grading</h4>
        <ul class="list-unstyled components">
            <li>
                <a href="<?php echo e(route('grading.index')); ?>" class="<?php echo e(Request::is('grading') && !Request::is('grading/*') ? 'active' : ''); ?>">
                    <i class="fa fa-home purple_color"></i>
                    <span>Grading Home</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('grading.assessments.index')); ?>" class="<?php echo e(Request::is('grading/assessments*') ? 'active' : ''); ?>">
                    <i class="fa fa-list purple_color"></i>
                    <span>Assessments</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('grading.mark-entry.index')); ?>" class="<?php echo e(Request::is('grading/mark-entry*') ? 'active' : ''); ?>">
                    <i class="fa fa-pencil purple_color"></i>
                    <span>Mark Entry</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('grading.report-cards.index')); ?>" class="<?php echo e(Request::is('grading/report-cards*') ? 'active' : ''); ?>">
                    <i class="fa fa-file-pdf-o purple_color"></i>
                    <span>Report Cards</span>
                </a>
            </li>
            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
            <li>
                <a href="#gradingSetupMenu" data-toggle="collapse" aria-expanded="<?php echo e(Request::is('grading/settings*') || Request::is('grading/subjects*') || Request::is('grading/class-subjects*') || Request::is('grading/assessment-types*') || Request::is('grading/weights*') || Request::is('grading/assignments*') ? 'true' : 'false'); ?>" class="dropdown-toggle">
                    <i class="fa fa-cog purple_color"></i>
                    <span>Setup</span>
                </a>
                <ul class="collapse list-unstyled <?php echo e(Request::is('grading/settings*') || Request::is('grading/subjects*') || Request::is('grading/class-subjects*') || Request::is('grading/assessment-types*') || Request::is('grading/weights*') || Request::is('grading/assignments*') ? 'show' : ''); ?>" id="gradingSetupMenu">
                    <li><a class="<?php echo e(Request::is('grading/settings*') ? 'active' : ''); ?>" href="<?php echo e(route('grading.settings.index')); ?>">Scheme &amp; Year</a></li>
                    <li><a class="<?php echo e(Request::is('grading/subjects*') ? 'active' : ''); ?>" href="<?php echo e(route('grading.subjects.index')); ?>">Subjects</a></li>
                    <li><a class="<?php echo e(Request::is('grading/class-subjects*') ? 'active' : ''); ?>" href="<?php echo e(route('grading.class-subjects.index')); ?>">Class Subjects</a></li>
                    <li><a class="<?php echo e(Request::is('grading/assessment-types*') ? 'active' : ''); ?>" href="<?php echo e(route('grading.assessment-types.index')); ?>">Assessment Types</a></li>
                    <li><a class="<?php echo e(Request::is('grading/weights*') ? 'active' : ''); ?>" href="<?php echo e(route('grading.weights.index')); ?>">Weights</a></li>
                    <li><a class="<?php echo e(Request::is('grading/assignments*') ? 'active' : ''); ?>" href="<?php echo e(route('grading.assignments.index')); ?>">Teacher Assignments</a></li>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\Users\Allan\smart_accountant2\resources\views/layouts/partials/sidebar/grading.blade.php ENDPATH**/ ?>