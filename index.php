<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>Stat</title>
<style>
body {
  font-size: 16px;
  overflow-x: full;
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
  max-height: 600px;
}
.parents, .opens, .closes, a {
  cursor: pointer;
  font-weight: bold;
}
.parents {
  font-size: 24px;
  margin-top: 5px;
  margin-bottom: 5px;
  padding-left: 5px;
  border-left: 6px solid #ccc;
  border-bottom: 1px solid #ccc;
}
.updowns {
  margin-left: 10px;
  font-size: 16px;
}
</style>
</head>
<body>
<?php

if (is_file('stat.png')) {
  $time = date("Y-m-d H:i", filemtime('stat.png'));
  print_r('<div id="parent-index" class="parents">stat.png ');
  print_r('<span class="times">(' . $time . ')</span>');
  print_r('<span id="updown-index" class="updowns"></span>');
  print_r('</div>');
  print_r('<div id="wrapper-index" class="wrappers openfirst">');
  print_r('<img src="stat.png" />');
  print_r('</div>');
};

$splits = [];

foreach (['txt', 'png'] as $ext) {
  foreach (glob('*.' . $ext) as $file) {
    if (!preg_match('/_/', $file)) continue;
    $split = preg_split('/_/', $file, 2);
    if (!isset($splits[$split[0]])) $splits[$split[0]] = [];
    array_push($splits[$split[0]], $split[1]);
  };
};

ksort($splits);

print_r('<div id="parent-toc" class="parents">table of contents');
print_r('<span id="updown-toc" class="updowns"></span>');
print_r('</div>');
print_r('<div id="wrapper-toc" class="wrappers openfirst">');
print_r('<ul id="toc">');
foreach ($splits as $skey => $selems) {
  $skey2 = $skey;
  if ($skey2 == '') $skey2 = '(misc)';
  print_r('<li><a id="link-' . $skey . '">' . $skey2 . '</a></li>');
}
print_r('</ul>');
print_r('</div>');

foreach ($splits as $skey => $selems) {

  $skey2 = $skey;
  if ($skey2 == '') $skey2 = '(misc)';

  print_r('<div id="parent-' . $skey . '" class="parents">');
  print_r($skey2);
  print_r('<span id="updown-' . $skey . '" class="updowns"></span>');
  print_r('</div>');
  print_r('<div id="wrapper-' . $skey . '" class="wrappers openfirst">');

  foreach ($selems as $selem) {

    $file = $skey . '_' . $selem;
    $id   = $skey . '_' . preg_split('/\./', $selem)[0];

    $time = date("Y-m-d H:i", filemtime($file));

    if (substr($file, -4) == '.png') {
      print_r('<span class="titles"><a href="' . $file . '">' . $file . '</a></span> ');
      print_r('<span class="times">(' . $time . ')</span>');
      $img = '<img src="' . $file . '" />';
      print_r('<div id="excerpt-' . $id . '" class="excerpts">' . $img . '</div>');
      print_r('<br />');
      continue;
    }; # png

    $lines = file($file);
    $nline = count($lines);

    $nlinemax  = 15;
    $nlinemax2 = 1000;

    $excerpt = '';
    $full    = '';
    $open    = '';
    $close   = '';

    for ($i = 0; $i < min($nline, $nlinemax2); $i++) {
      $full = $full . htmlspecialchars($lines[$i]);
    };

    for ($i = 0; $i < min($nline, $nlinemax); $i++) {
      $excerpt = $excerpt . htmlspecialchars($lines[$i]);
    };

    if ($nline > $nlinemax) {
      $open  = '... (total ' . $nline . ' lines; click here to open)';
      $close = '(total ' . $nline . ' lines; click here to collapse)';
    };

    print_r('<span class="titles"><a href="' . $file . '">' . $file . '</a></span> ');

    print_r('<span class="times">(' . $time . ')</span>');

    print_r('<pre id="excerpt-' . $id . '" class="excerpts">' . $excerpt . '</pre>');

    if ($open != '') {
      print_r('<pre id="full-'  . $id . '" class="fulls">'  . $full  . '</pre>');
      print_r('<div id="open-'  . $id . '" class="opens">'  . $open  . '</div>');
      print_r('<div id="close-' . $id . '" class="closes">' . $close . '</div>');
    }

    print_r('<br />');

  };

  print_r('</div>');

};

?>

<script>

const w_openclose = function(idsuf, isopen, isscroll) {

  const w = document.querySelector('#wrapper-' + idsuf);
  const u = document.querySelector('#updown-'  + idsuf);
  const p = document.querySelector('#parent-'  + idsuf);

  if (w.style.display == '') w.style.display = 'none';
  if (isopen === undefined) isopen = (w.style.display == 'none');

  w.style.display = isopen ? 'block' : 'none';
  u.innerHTML = isopen ? '[&#x25b2;]' : '[&#x25bc;]';
  if (isscroll) p.scrollIntoView();

};

document.querySelectorAll('.openfirst').forEach((c) => {
  const idsuf = c.id.replace(/^wrapper-/, '');
  w_openclose(idsuf, true, false);
});

document.querySelectorAll('.closefirst').forEach((c) => {
  const idsuf = c.id.replace(/^wrapper-/, '');
  w_openclose(idsuf, false, false);
});

document.querySelectorAll('[id^=link-]').forEach((l) => {

  const idsuf = l.id.replace(/^link-/, '');

  l.addEventListener('mouseup', () => {
    w_openclose(idsuf, true, true);
  });

});

document.querySelectorAll('[id^=parent-]').forEach((p) => {

  const idsuf = p.id.replace(/^parent-/, '');

  p.addEventListener('mouseup', () => {
    w_openclose(idsuf);
  });

});

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
    c.style.display = 'block';
  });

  c.addEventListener('mouseup', () => {
    e.style.display = 'block';
    o.style.display = 'block';
    f.style.display = 'none';
    c.style.display = 'none';
  });

});

</script>
</body>
</html>

