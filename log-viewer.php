<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>log-viewer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<style>
body {
  font-size: 16px;
  overflow-x: full;
  padding: 10px;
}
.titles {
  font-size: 20px;
  font-weight: bold;
}
pre {
  font-size: 12px;
  margin-top: 2px;
  background-color: #eee;
  font-family: Consolas, monospace;
  width: 100%;
  overflow: auto;
}
.fulls, .closes {
  display: none;
}
.fulls {
  max-height: 300px;
}
.opens, .closes, a {
  cursor: pointer;
  font-weight: bold;
}
</style>
</head>
<body>

<?php

foreach (glob('*.*') as $file) {

  if (preg_match('/\.(cgi|php)/', substr($file, -4))) continue;

  $id   = preg_split('/\./', $file)[0];
  $time = date("Y-m-d H:i", filemtime($file));

  print_r('<span class="titles"><a href="' . $file . '">' . $file . '</a></span> ');
  print_r('<span class="times">(' . $time . ')</span>');

  if (preg_match('/\.(gif|jpg|png)/', substr($file, -4))) {
    print_r('<div id="excerpt-' . $id . '" class="excerpts"><img src="' . $file . '" /></div>');
    print_r('<br />');
    continue;
  }

  $lines = file($file);
  $nline = count($lines);

  $nlinemax  = 10;
  $nlinemax2 = 1000;

  $excerpt = '';
  $full    = '';

  for ($i = max($nline - $nlinemax, 0); $i < $nline; $i++) {
    $excerpt = $excerpt . htmlspecialchars($lines[$i]);
  }

  for ($i = max($nline - $nlinemax2, 0); $i < $nline; $i++) {
    $full = $full . htmlspecialchars($lines[$i]);
  }

  if ($nline > $nlinemax) {
    if ($nline > $nlinemax2) {
      $open  = '(total ' . $nline . ' lines; click here to show ' . $nlinemax2 .' lines)';
    } else {
      $open  = '(total ' . $nline . ' lines; click here to show)';
    }
    $close = '(total ' . $nline . ' lines; click here to collapse)';

    print_r(' <span id="open-'  . $id . '" class="opens">'  . $open  . '</span>');
    print_r(' <span id="close-' . $id . '" class="closes">' . $close . '</span>');
    print_r('<pre id="full-'  . $id . '" class="fulls">'  . $full  . '</pre>');
  }

  print_r('<pre id="excerpt-' . $id . '" class="excerpts">' . $excerpt . '</pre>');
  print_r('<br />');

}

?>

<script>

document.querySelectorAll('[id^=excerpt-]').forEach((e) => {

  const idsuf = e.id.replace(/^excerpt-/, '');

  const f = document.querySelector('#full-' + idsuf);

  if (f === null) return true;

  const o = document.querySelector('#open-'  + idsuf);
  const c = document.querySelector('#close-' + idsuf);

  o.addEventListener('mouseup', () => {
    e.style.display = 'none';
    o.style.display = 'none';
    f.style.display = 'block';
    f.scrollTop = f.scrollHeight;
    c.style.display = 'inline';
  });

  c.addEventListener('mouseup', () => {
    e.style.display = 'block';
    o.style.display = 'inline';
    f.style.display = 'none';
    c.style.display = 'none';
  });

});

</script>
</body>
</html>

