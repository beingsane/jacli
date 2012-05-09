=begin
** Form generated from reading ui file 'jacli.ui'
**
** Created: Di. Mai 8 20:44:52 2012
**      by: Qt User Interface Compiler version 4.7.4
**
** WARNING! All changes made in this file will be lost when recompiling ui file!
=end

require 'korundum4'

class Ui_MainWindow
    attr_reader :centralwidget
    attr_reader :list_apps
    attr_reader :list_versions
    attr_reader :button_quit
    attr_reader :button_deploy
    attr_reader :label
    attr_reader :label_2
    attr_reader :log_console
    attr_reader :txt_target
    attr_reader :label_3
    attr_reader :scrollArea
    attr_reader :scrollAreaWidgetContents
    attr_reader :verticalLayoutWidget
    attr_reader :verticalLayout
    attr_reader :label_7
    attr_reader :scrollArea_2
    attr_reader :scrollAreaWidgetContents_2
    attr_reader :verticalLayoutWidget_2
    attr_reader :verticalLayout_2
    attr_reader :label_4
    attr_reader :lineEdit
    attr_reader :label_5
    attr_reader :label_6
    attr_reader :scrollArea_3
    attr_reader :scrollAreaWidgetContents_3
    attr_reader :verticalLayoutWidget_3
    attr_reader :verticalLayout_3
    attr_reader :label_8
    attr_reader :menubar
    attr_reader :statusbar

    def setupUi(mainWindow)
    if mainWindow.objectName.nil?
        mainWindow.objectName = "mainWindow"
    end
    mainWindow.resize(600, 600)
    @font = Qt::Font.new
    @font.pointSize = 10
    mainWindow.font = @font
    @centralwidget = Qt::Widget.new(mainWindow)
    @centralwidget.objectName = "centralwidget"
    @list_apps = Qt::ListWidget.new(@centralwidget)
    @list_apps.objectName = "list_apps"
    @list_apps.geometry = Qt::Rect.new(10, 30, 131, 101)
    @list_versions = Qt::ListWidget.new(@centralwidget)
    @list_versions.objectName = "list_versions"
    @list_versions.geometry = Qt::Rect.new(150, 30, 111, 101)
    @button_quit = Qt::PushButton.new(@centralwidget)
    @button_quit.objectName = "button_quit"
    @button_quit.geometry = Qt::Rect.new(400, 110, 81, 21)
    @button_deploy = Qt::PushButton.new(@centralwidget)
    @button_deploy.objectName = "button_deploy"
    @button_deploy.geometry = Qt::Rect.new(270, 60, 211, 41)
    @font1 = Qt::Font.new
    @font1.pointSize = 16
    @button_deploy.font = @font1
    @label = Qt::Label.new(@centralwidget)
    @label.objectName = "label"
    @label.geometry = Qt::Rect.new(10, 10, 81, 16)
    @sizePolicy = Qt::SizePolicy.new(Qt::SizePolicy::Preferred, Qt::SizePolicy::Preferred)
    @sizePolicy.setHorizontalStretch(0)
    @sizePolicy.setVerticalStretch(0)
    @sizePolicy.heightForWidth = @label.sizePolicy.hasHeightForWidth
    @label.sizePolicy = @sizePolicy
    @font2 = Qt::Font.new
    @font2.pointSize = 14
    @label.font = @font2
    @label_2 = Qt::Label.new(@centralwidget)
    @label_2.objectName = "label_2"
    @label_2.geometry = Qt::Rect.new(150, 10, 81, 16)
    @sizePolicy.heightForWidth = @label_2.sizePolicy.hasHeightForWidth
    @label_2.sizePolicy = @sizePolicy
    @label_2.font = @font2
    @log_console = Qt::PlainTextEdit.new(@centralwidget)
    @log_console.objectName = "log_console"
    @log_console.geometry = Qt::Rect.new(10, 430, 471, 131)
    @font3 = Qt::Font.new
    @font3.family = "Monospace"
    @font3.pointSize = 10
    @log_console.font = @font3
    @log_console.styleSheet = "background-color: rgb(0, 0, 0);\n" \
"color: rgb(117, 255, 104);"
    @log_console.plainText = "JACLI Console"
    @txt_target = Qt::LineEdit.new(@centralwidget)
    @txt_target.objectName = "txt_target"
    @txt_target.geometry = Qt::Rect.new(269, 30, 141, 21)
    @font4 = Qt::Font.new
    @font4.pointSize = 12
    @txt_target.font = @font4
    @label_3 = Qt::Label.new(@centralwidget)
    @label_3.objectName = "label_3"
    @label_3.geometry = Qt::Rect.new(270, 10, 131, 21)
    @label_3.font = @font2
    @scrollArea = Qt::ScrollArea.new(@centralwidget)
    @scrollArea.objectName = "scrollArea"
    @scrollArea.geometry = Qt::Rect.new(10, 160, 241, 271)
    @scrollArea.verticalScrollBarPolicy = Qt::ScrollBarAlwaysOn
    @scrollArea.widgetResizable = false
    @scrollAreaWidgetContents = Qt::Widget.new()
    @scrollAreaWidgetContents.objectName = "scrollAreaWidgetContents"
    @scrollAreaWidgetContents.geometry = Qt::Rect.new(0, 0, 218, 265)
    @sizePolicy.heightForWidth = @scrollAreaWidgetContents.sizePolicy.hasHeightForWidth
    @scrollAreaWidgetContents.sizePolicy = @sizePolicy
    @verticalLayoutWidget = Qt::Widget.new(@scrollAreaWidgetContents)
    @verticalLayoutWidget.objectName = "verticalLayoutWidget"
    @verticalLayoutWidget.geometry = Qt::Rect.new(0, 0, 191, 161)
    @verticalLayout = Qt::VBoxLayout.new(@verticalLayoutWidget)
    @verticalLayout.spacing = 5
    @verticalLayout.margin = 4
    @verticalLayout.objectName = "verticalLayout"
    @verticalLayout.setContentsMargins(0, 0, 0, 0)
    @label_7 = Qt::Label.new(@verticalLayoutWidget)
    @label_7.objectName = "label_7"

    @verticalLayout.addWidget(@label_7)

    @scrollArea.setWidget(@scrollAreaWidgetContents)
    @scrollArea_2 = Qt::ScrollArea.new(@centralwidget)
    @scrollArea_2.objectName = "scrollArea_2"
    @scrollArea_2.geometry = Qt::Rect.new(380, 150, 231, 271)
    @scrollArea_2.verticalScrollBarPolicy = Qt::ScrollBarAlwaysOn
    @scrollArea_2.widgetResizable = false
    @scrollAreaWidgetContents_2 = Qt::Widget.new()
    @scrollAreaWidgetContents_2.objectName = "scrollAreaWidgetContents_2"
    @scrollAreaWidgetContents_2.geometry = Qt::Rect.new(0, 0, 208, 265)
    @verticalLayoutWidget_2 = Qt::Widget.new(@scrollAreaWidgetContents_2)
    @verticalLayoutWidget_2.objectName = "verticalLayoutWidget_2"
    @verticalLayoutWidget_2.geometry = Qt::Rect.new(0, 0, 151, 121)
    @verticalLayout_2 = Qt::VBoxLayout.new(@verticalLayoutWidget_2)
    @verticalLayout_2.spacing = 5
    @verticalLayout_2.margin = 4
    @verticalLayout_2.objectName = "verticalLayout_2"
    @verticalLayout_2.sizeConstraint = Qt::Layout::SetNoConstraint
    @verticalLayout_2.setContentsMargins(0, 0, 0, 0)
    @label_4 = Qt::Label.new(@verticalLayoutWidget_2)
    @label_4.objectName = "label_4"
    @label_4.enabled = true

    @verticalLayout_2.addWidget(@label_4)

    @lineEdit = Qt::LineEdit.new(@verticalLayoutWidget_2)
    @lineEdit.objectName = "lineEdit"

    @verticalLayout_2.addWidget(@lineEdit)

    @scrollArea_2.setWidget(@scrollAreaWidgetContents_2)
    @label_5 = Qt::Label.new(@centralwidget)
    @label_5.objectName = "label_5"
    @label_5.geometry = Qt::Rect.new(10, 140, 111, 16)
    @label_6 = Qt::Label.new(@centralwidget)
    @label_6.objectName = "label_6"
    @label_6.geometry = Qt::Rect.new(390, 130, 141, 16)
    @scrollArea_3 = Qt::ScrollArea.new(@centralwidget)
    @scrollArea_3.objectName = "scrollArea_3"
    @scrollArea_3.geometry = Qt::Rect.new(260, 140, 241, 271)
    @scrollArea_3.verticalScrollBarPolicy = Qt::ScrollBarAlwaysOn
    @scrollArea_3.widgetResizable = false
    @scrollAreaWidgetContents_3 = Qt::Widget.new()
    @scrollAreaWidgetContents_3.objectName = "scrollAreaWidgetContents_3"
    @scrollAreaWidgetContents_3.geometry = Qt::Rect.new(0, 0, 218, 265)
    @sizePolicy.heightForWidth = @scrollAreaWidgetContents_3.sizePolicy.hasHeightForWidth
    @scrollAreaWidgetContents_3.sizePolicy = @sizePolicy
    @verticalLayoutWidget_3 = Qt::Widget.new(@scrollAreaWidgetContents_3)
    @verticalLayoutWidget_3.objectName = "verticalLayoutWidget_3"
    @verticalLayoutWidget_3.geometry = Qt::Rect.new(0, 0, 191, 161)
    @verticalLayout_3 = Qt::VBoxLayout.new(@verticalLayoutWidget_3)
    @verticalLayout_3.spacing = 5
    @verticalLayout_3.margin = 4
    @verticalLayout_3.objectName = "verticalLayout_3"
    @verticalLayout_3.setContentsMargins(0, 0, 0, 0)
    @label_8 = Qt::Label.new(@verticalLayoutWidget_3)
    @label_8.objectName = "label_8"

    @verticalLayout_3.addWidget(@label_8)

    @scrollArea_3.setWidget(@scrollAreaWidgetContents_3)
    mainWindow.centralWidget = @centralwidget
    @menubar = Qt::MenuBar.new(mainWindow)
    @menubar.objectName = "menubar"
    @menubar.geometry = Qt::Rect.new(0, 0, 600, 20)
    mainWindow.setMenuBar(@menubar)
    @statusbar = Qt::StatusBar.new(mainWindow)
    @statusbar.objectName = "statusbar"
    mainWindow.statusBar = @statusbar

    retranslateUi(mainWindow)

    Qt::MetaObject.connectSlotsByName(mainWindow)
    end # setupUi

    def setup_ui(mainWindow)
        setupUi(mainWindow)
    end

    def retranslateUi(mainWindow)
    mainWindow.windowTitle = Qt::Application.translate("MainWindow", "JACLI", nil, Qt::Application::UnicodeUTF8)
    @button_quit.text = Qt::Application.translate("MainWindow", "Quit", nil, Qt::Application::UnicodeUTF8)
    @button_deploy.text = Qt::Application.translate("MainWindow", "Deploy It ...", nil, Qt::Application::UnicodeUTF8)
    @label.text = Qt::Application.translate("MainWindow", "Application", nil, Qt::Application::UnicodeUTF8)
    @label_2.text = Qt::Application.translate("MainWindow", "Version", nil, Qt::Application::UnicodeUTF8)
    @txt_target.text = ''
    @label_3.text = Qt::Application.translate("MainWindow", "Target directory", nil, Qt::Application::UnicodeUTF8)
    @label_7.text = Qt::Application.translate("MainWindow", "TextLabel", nil, Qt::Application::UnicodeUTF8)
    @label_4.text = Qt::Application.translate("MainWindow", "TextLabel", nil, Qt::Application::UnicodeUTF8)
    @label_5.text = Qt::Application.translate("MainWindow", "Default settings", nil, Qt::Application::UnicodeUTF8)
    @label_6.text = Qt::Application.translate("MainWindow", "Application Settings", nil, Qt::Application::UnicodeUTF8)
    @label_8.text = Qt::Application.translate("MainWindow", "TextLabel", nil, Qt::Application::UnicodeUTF8)
    end # retranslateUi

    def retranslate_ui(mainWindow)
        retranslateUi(mainWindow)
    end

end

module Ui
    class MainWindow < Ui_MainWindow
    end
end  # module Ui

if $0 == __FILE__
    about = KDE::AboutData.new("mainwindow", "MainWindow", KDE.ki18n(""), "0.1")
    KDE::CmdLineArgs.init(ARGV, about)
    a = KDE::Application.new
    u = Ui_MainWindow.new
    w = Qt::MainWindow.new
    u.setupUi(w)
    a.topWidget = w
    w.show
    a.exec
end
