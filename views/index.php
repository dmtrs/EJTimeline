<div id="timelineLimiter"> <!-- Hides the overflowing timelineScroll div -->
    <div id="timelineScroll"> <!-- Contains the timeline and expands to fit -->
    <?php
    foreach($events as $y=>$e)
    {
        echo "<div class='event'>";
        echo "<div class='eventHeading' >$y</div>";
        echo '<ul class="eventList">';
        $config['dataProvider']= $e;
        $config['viewData']= array('dt'=>$y);
        $this->widget('zii.widgets.CListView', $config);
        echo "</ul>";
        echo "</div>";
    }?>
        <div class="clear"></div>
        </div>
        
        <div id="scroll"> <!-- The year time line -->
            <div id="centered"> <!-- Sized by jQuery to fit all the years -->
	            <div id="highlight"></div> <!-- The light blue highlight shown behind the years -->
	            <?php foreach(array_keys($events) as $k) 
                {
                    echo "<div class='scrollPoints'>$k</div>\n";
                }?>
                <div class="clear"></div>
            </div>
        </div>
        
        <div id="slider"> <!-- The slider container -->
        	<div id="bar"> <!-- The bar that can be dragged -->
            	<div id="barLeft"></div>  <!-- Left arrow of the bar -->
                <div id="barRight"></div>  <!-- Right arrow, both are styled with CSS -->
          </div>
        </div>
        
    </div> 
</div>
