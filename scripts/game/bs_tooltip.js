var originalLeave = $.fn.tooltip.Constructor.prototype.leave;
$.fn.tooltip.Constructor.prototype.leave = function(obj){
  var self = obj instanceof this.constructor ?
    obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
  var container, timeout;

  originalLeave.call(this, obj);

  if(obj.currentTarget) {
    container = $(obj.currentTarget).siblings('.tooltip')
    timeout = self.timeout;
    container.one('mouseenter', function(){
      //We entered the actual popover – call off the dogs
      clearTimeout(timeout);
      //Let's monitor popover content instead
      container.one('mouseleave', function(){
        $.fn.tooltip.Constructor.prototype.leave.call(self, self);
      });
    })
  }
};


$('body').tooltip({ selector: '[data-toggle]', trigger: 'click hover', placement: 'bottom auto', delay: {show: 50, hide: 400}});
