var expander = {
    init: function() {
        this.bindExpands();
    },
    bindExpands: function() {
        let self = this;
        
        $('.exp-caption').click(function() {
            let targetString = $(this).attr('data-target'),
                $thisBlock = $(this).closest('.exp-content-block'),
                behavior = $thisBlock.attr('data-behavior');
            
            if (targetString) {
                if (self.isBlockOpened($thisBlock)) {
                    self.closeBlock($thisBlock);
                    
                } else {
                    if (behavior == 'accordion') {
                        //self.closeAllBlockExcept($thisBlock);
                        let siblings = $thisBlock.siblings();
                        for (let block in siblings) {
                            self.closeBlock($(block));
                        }
                    }
                    self.openBlock($thisBlock);
                }                
            }
        });
    },
    
    initExpander: function() {
        if(window.location.hash) {
            let hash = window.location.hash;

            if ($(hash).length > 0 && $(hash).hasClass('exp-content-block')) {
                this.openBlock($(hash));
            }
        }
    },
    
    openBlock: function($block) {
        var block = $block.find('.exp-caption');
        
        let contentBlockIds = $block.find('.exp-caption').attr('data-target').split(',');
    
        $block.addClass('opened');
        $block.find('.exp-expander').removeClass('icon-group-closed').addClass('icon-group-opened');

        for (let contentBlockId of contentBlockIds) {
            $('#'+contentBlockId).slideDown();
        }
    },
    closeBlock: function($block) {
        let dataTarget = $block.find('.exp-caption').attr('data-target');
        
        if (dataTarget) {
            contentBlockIds = dataTarget.split(',');

            $block.removeClass('opened');
            $block.find('.exp-expander').removeClass('icon-group-opened').addClass('icon-group-closed');
        
            for (let contentBlockId of contentBlockIds) {
                $('#'+contentBlockId).slideUp();
            }
        }
    },
    closeAllBlockExcept: function($exceptedBlock) {
        /*$expContainer.find('.exp-content').not($exceptedBlock).hide();
        $expContainer.find('.exp-caption').not($exceptedBlock).removeClass('opened');
        $expContainer.find('.exp-expander').not($exceptedBlock).removeClass('icon-group-opened').addClass('icon-group-closed');  */
        let $expContainer = $exceptedBlock.closest('.exp-content-container');
            blocks = $expContainer.find('.exp-content-block').not($exceptedBlock);
            
        for (let block of blocks) {
            this.closeBlock($(block));
        }
    },
    
    isBlockOpened: function($block) {
        return $block.hasClass('opened');
    },    
    addExpandControls: function() {
        $('.exp-caption').append('<i class="exp-expander icon icon-group-closed"></i>');
    }
};