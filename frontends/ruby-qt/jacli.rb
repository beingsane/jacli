require 'jacli_ui'
require 'rexml/document'

class Jacli < Qt::MainWindow

  slots 'deploy()', 'fill_versions(QListWidgetItem *)'

  def initialize(parent = nil)
    super(parent)

    @ui = Ui::MainWindow.new
    @ui.setup_ui(self)
    @base_dir = "/home/jtester/repos/jacli/config"

    # puts File.expand_path File.dirname(__FILE__)
    #               puts Dir.pwd

    Dir[@base_dir + "/repositories/*.xml"].each() do |a|
      i = Qt::ListWidgetItem.new(@ui.list_apps)
      i.text = File.basename(a, '.xml')
      i.statusTip = a
    end

    self.read_common_config

    Qt::Object.connect(@ui.list_apps,
                       SIGNAL('itemClicked(QListWidgetItem *)'),
                       self, SLOT('fill_versions(QListWidgetItem *)'))

    Qt::Object.connect(@ui.button_deploy, SIGNAL(:clicked), self, SLOT(:deploy))
    Qt::Object.connect(@ui.button_quit, SIGNAL(:clicked), $a, SLOT(:quit))
  end

  def read_common_config()
    file = File.open(@base_dir + '/configuration.php')

    y_pos = 5

    while (line = file.gets)
      parts = line.strip.split

      if 'public' == parts[0]
        k = parts[1].tr('$', '')
        v = parts[3].chop.tr("'", '')

        l = Qt::Label.new(@ui.verticalLayoutWidget)
        l.objectName = 'lbl_' + k
        l.text = k
        l.geometry = Qt::Rect.new(5, y_pos, 191, 16)

        e = Qt::LineEdit.new(@ui.verticalLayoutWidget)
        e.objectName = 'txt_' + k
        e.text = v
        e.geometry = Qt::Rect.new(5, y_pos + 15, 201, 20)

        y_pos += 35
      end
    end

    @ui.verticalLayoutWidget.setGeometry(0, 0, 205, y_pos + 5)
    @ui.scrollAreaWidgetContents.adjustSize()

    file.close
  end

  def read_application_config(application)
    file = File.open(@base_dir + '/application/' + application.text + '.dist.php')

    y_pos = 5

    while child = @ui.verticalLayout_3.takeAt(0)

      puts child
    end

    puts 'second try'

    @ui.verticalLayoutWidget_3.children.each do |a|
   #   @ui.verticalLayout_3.removeWidget(a)
      if 'Qt::Label' == a.class
        a.remove
        a.clear
      end
      puts a
#      a.hide
    end


    while (line = file.gets)
      parts = line.strip.split

      if 'public' == parts[0]
        k = parts[1].tr('$', '')
        v = parts[3].chop.tr("'", '')

        al = Qt::Label.new(@ui.verticalLayoutWidget_3)
        al.objectName = 'lbl_x' + k
        al.text = k
        al.geometry = Qt::Rect.new(10, y_pos, 191, 16)
        al.show

        e = Qt::LineEdit.new(@ui.verticalLayoutWidget_3)
        e.objectName = 'txt_x' + k
        e.text = v
        e.geometry = Qt::Rect.new(10, y_pos + 15, 201, 20)
        e.show

        y_pos += 35
      end
    end

 #   @ui.verticalLayoutWidget_3.setGeometry(0, 0, 205, y_pos + 500)
    @ui.verticalLayoutWidget_3.update
    @ui.verticalLayoutWidget_3.show

    @ui.scrollAreaWidgetContents_3.adjustSize
    @ui.scrollAreaWidgetContents_3.update
    @ui.scrollAreaWidgetContents_3.show

    @ui.scrollArea_3.update
    @ui.scrollArea_3.show

    file.close
  end

  def fill_versions app_name
    @ui.list_versions.clear()

    doc = REXML::Document.new File.read app_name.statusTip

    doc.elements.each('repository/versions/version') do |p|
      i = Qt::ListWidgetItem.new(@ui.list_versions)
      i.text = p.elements['version'].text
    end

    self.read_application_config app_name
  end

  def deploy()
    puts 'Deploying...'

    cmd = 'jacli'

    cmd += ' --target ' + @ui.txt_target.text

    selected = @ui.list_apps.selectedItems()[0]

    if selected
      cmd += ' --application ' + selected.text
    end

    selected = @ui.list_versions.selectedItems()[0]

    if selected
      cmd += ' --version ' + selected.text
    end

    cmd += '&'

    puts cmd

    IO.popen("#{cmd} 2>&1") do |f|
      while line = f.gets do
        @ui.log_console.plainText += line
      end
    end

    if $? == 0
      @ui.log_console.plainText += "\nSuccess"
    else
      puts "There was a failure"
    end
  end

end
