
<h1>Тест проверки на спам</h1>
<table border="0">
  <tr>
    <th>Слово/фраза</th>
    <th>Вероятность</th>
    <th>Спам</th>
  </tr>
  <?php foreach ($texts as $text) { ?>
  <tr>
    <td><strong><?php echo $text ?></strong></td>
    <td><?php echo $spam->isItSpam_v2($text,'spam') ?>%</td>
    <td>spam</td>
  </tr>
  <?php  } ?>	
</table>

<br />
<br />
<br />
	
	<h1>Тест проверки на не спам</h1>
<table border="0">
  <tr>
    <th>Слово/фраза</th>
    <th>Вероятность</th>
    <th>Спам</th>
  </tr>
  <?php foreach ($texts as $text) { ?>
  <tr>
    <td><strong><?php echo $text ?></strong></td>
    <td><?php echo $spam->isItSpam_v2($text,'1') ?>%</td>
    <td>not spam</td>
  </tr>
  <?php  } ?>	
</table>

