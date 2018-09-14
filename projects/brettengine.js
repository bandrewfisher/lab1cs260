/**Keyboard functions**/
var KEY_LEFT = 37;
var KEY_RIGHT = 39;
var KEY_UP = 38;
var KEY_DOWN = 40;
var KEY_SPACE = 32;
var KEY_w = 87;
var KEY_s = 83;

var Keyboard = function() {
    this.left = false;
    this.right = false;
    this.up = false;
    this.down = false;
    this.space = false;
    this.w = false;
    this.s = false;
};

var KeyState = {};
function initializeKeyboard() {

    KeyState = new Keyboard();
    $(document).keydown(function(e) {
        if (e.keyCode == KEY_LEFT) {
            KeyState.left = true;
        }

        if (e.keyCode == KEY_RIGHT) {
            KeyState.right = true;
        }

        if (e.keyCode == KEY_UP) {
            KeyState.up = true;
        }

        if (e.keyCode == KEY_DOWN) {
            KeyState.down = true;
        }

        if(e.keyCode == KEY_SPACE) {
            KeyState.space = true;
        }

        if(e.keyCode == KEY_w) {

            KeyState.w = true;
        }

        if(e.keyCode == KEY_s) {
            KeyState.s = true;
        }

    });


    $(document).keyup(function(e) {

        if (e.keyCode == KEY_LEFT) {
            KeyState.left = false;
        }

        if (e.keyCode == KEY_RIGHT) {
            KeyState.right = false;
        }

        if (e.keyCode == KEY_UP) {
            KeyState.up = false;
        }

        if (e.keyCode == KEY_DOWN) {
            KeyState.down = false;
        }

        if(e.keyCode == KEY_SPACE) {
            KeyState.space = false;
        }

        if(e.keyCode == KEY_w) {
            KeyState.w = false;
        }

        if(e.keyCode == KEY_s) {
            KeyState.s = false;
        }

    });
}

/**End keyboard functions and variables**/

/**Context and canvas functions**/
var Context = function(canvas_tag_id){

    var canvas = document.getElementById(canvas_tag_id);
    return canvas.getContext('2d');
};

function initializeCanvas(context, width, height) {
    context.canvas.width = width;
    context.canvas.height = height;
}
/**End context and canvas functions**/

/**Math functions**/

var EngineMath = {
    //Convert degrees to radians
    toRadians: function(degrees) {
        return degrees*Math.PI / 180;
    },

    //Put an angle(degrees) to its corresponding angle between 0 and 360
    degTo360: function(degrees) {
        degrees %= 360;
        if(degrees < 0) {
            degrees += 360;
        }
        return degrees;
    }
};


/**End math functions**/

//Initialize everything
//Context: context for the canvas
//Width and height: width and height of canvas
function init(context, width, height) {
    initializeKeyboard();
    initializeCanvas(context,width,height);
}