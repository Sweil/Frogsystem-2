<!--section-start::MAIN--><body>
  <div id="main">

    <div id="header">
      <h1 id="title">&nbsp;$VAR(page_title)</h1>
    </div>

    <div id="menu_left">
      <p>
        <a href="$URL(download[cat_id=4 keyword=test])">$URL(download[cat_id=4 keyword=test 1])</a>
      </p>
    $APP(mini-search.php)
    $NAV(left.nav)
    [%feeds%]
    $DATE(d.m.Y H:i \U\h\r)<br>
    $DATE(Y-m-d[1216908484])
    </div>

    <div id="menu_right">
      $APP(user-menu.php)
      $APP(preview-image.php)
      $APP(shop-system.php)
      $APP(poll-system.php[random])
      $APP(affiliates.php)
      $APP(mini-statistics.php)
    </div>

    <div id="content">
      <div id="content_inner">
        $APP(announcement.php)
        {..content..}
      </div>
    </div>

    <div id="footer">
      <span class="copyright">&bdquo;Light Frog&ldquo;-Style &copy; Stoffel &amp; Sweil | Frog-Photo &copy; <a href="http://www.flickr.com/photos/joi/1157708196/" target="_blank">Joi</a><br>
       {..copyright..}</span>
    </div>

  </div>
</body><!--section-end::MAIN-->

<!--section-start::MATRIX-->{..doctype..}
<html lang="{..language..}">
  <head>
    {..base_tag..}
    {..title_tag..}
    {..meta_tags..}
    {..css_links..}
    {..favicon_link..}
    {..feed_link..}
    {..jquery..}
    {..jquery-ui..}
    {..javascript..}
  </head>
  {..body..}
</html><!--section-end::MATRIX-->

<!--section-start::DOCTYPE--><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><!--section-end::DOCTYPE-->

