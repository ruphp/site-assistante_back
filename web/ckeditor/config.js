CKEDITOR.plugins.addExternal('youtube', '/ckeditor/youtube/');
CKEDITOR.plugins.addExternal('html5video', '/ckeditor/html5video/');
CKEDITOR.plugins.addExternal('templates', '/ckeditor/templates/');

CKEDITOR.editorConfig = function(config) {
    config.language = 'ru';
    config.extraPlugins = 'youtube,html5video,widget,widgetselection,clipboard,lineutils,templates,justify,font';
    config.youtube_responsive = true;
};