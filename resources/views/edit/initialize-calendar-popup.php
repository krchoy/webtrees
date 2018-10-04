<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?php View::push('javascript') ?>
<script>
  cal_setMonthNames(
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'January')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'February')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'March')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'April')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'May')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'June')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'July')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'August')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'September')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'October')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'November')) ?>,
        <?= json_encode(I18N::translateContext('NOMINATIVE', 'December')) ?>
  );

  cal_setDayHeaders(
        <?= json_encode(I18N::translate('Sun')) ?>,
        <?= json_encode(I18N::translate('Mon')) ?>,
        <?= json_encode(I18N::translate('Tue')) ?>,
        <?= json_encode(I18N::translate('Wed')) ?>,
        <?= json_encode(I18N::translate('Thu')) ?>,
        <?= json_encode(I18N::translate('Fri')) ?>,
        <?= json_encode(I18N::translate('Sat')) ?>
  );

  cal_setWeekStart(" . I18N::firstDay() . ");
</script>
<?php View::endpush() ?>
