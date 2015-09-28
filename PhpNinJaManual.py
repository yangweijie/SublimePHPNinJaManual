# -*- coding: utf-8 -*-

import sublime, sublime_plugin
import styled_popup
import codecs,os,subprocess,threading,json,webbrowser,base64
from subprocess import PIPE

packages_path = os.path.split(os.path.realpath(__file__))[0]

def fs_reader(path):
	return codecs.open(path, mode='r', encoding='utf8').read()

def show_outpanel(self, name, string, readonly = True):
	self.output_view = self.window.get_output_panel(name)
	self.output_view.run_command('append', {'characters': string, 'force': True, 'scroll_to_end': True})
	if readonly:
		self.output_view.set_read_only(True)
	show_panel_on_build = sublime.load_settings("Preferences.sublime-settings").get("show_panel_on_build", True)
	if show_panel_on_build:
		self.window.run_command("show_panel", {"panel": "output." + name})

def open_tab(url):
	webbrowser.open_new_tab(url)

class PhpNinJaManualCommand(sublime_plugin.TextCommand):
	def run(self, edit):
		return ''

class show_php_document(PhpNinJaManualCommand, sublime_plugin.TextCommand):
	def run(self, edit):
		region = self.view.sel()[0]
		if region.begin() != region.end():
			function = self.view.substr(region)
			thread = find_comment(function,self.view)
			thread.start()
			ThreadProgress(thread, 'Is excuting', 'Finding Done')
		else:
			sublime.status_message('must be a word')

class find_comment(threading.Thread):
	def __init__(self, word, view):
		self.word = word
		self.view = view
		threading.Thread.__init__(self)

	def run(self):
		settings = sublime.load_settings('PhpNinJaManual.sublime-settings')
		print(settings.get('lang'))
		lang = settings.get('lang')
		path = packages_path + os.sep + 'php_docs' + os.sep + lang + os.sep
		db_file = path + 'doc.sqlite'
		html = """Each <span class="keyword">$element $count</span> within
			  the <span class="entity name class">html</span> can be styled
			  individually using common <span class="string quoted">scope</span> names.
			  Simply wrap each element to be styled in a span and apply the
			  <span class="comment line">css classes</span> for each scope."""
		html = self.get_comment(self.word, lang)
		# styled_popup.show_popup(self.view, html)
		return

	def get_comment(self, word, lang):
		function = self.word
		command_text = 'php "' + packages_path + os.sep + 'index.php" "Doc/find/function/' + function + '/lang/' + lang + '"'
		print(command_text)
		cloums = os.popen(command_text)
		data = cloums.read()
		if data:
			self.window = self.view.window()
			tmp = sublime.decode_value(data).replace('\\n', '<br>')
			print(tmp)
			styled_popup.show_popup(self.view, tmp, on_navigate=self.nav, max_width=700)
		else:
			sublime.status_message('not found')

	def nav(self, url):
		open_tab(url)

class ThreadProgress():
	"""
	Animates an indicator, [=   ], in the status area while a thread runs

	:param thread:
		The thread to track for activity

	:param message:
		The message to display next to the activity indicator

	:param success_message:
		The message to display once the thread is complete
	"""

	def __init__(self, thread, message, success_message):
		self.thread = thread
		self.message = message
		self.success_message = success_message
		self.addend = 1
		self.size = 8
		sublime.set_timeout(lambda: self.run(0), 100)

	def run(self, i):
		if not self.thread.is_alive():
			if hasattr(self.thread, 'result') and not self.thread.result:
				sublime.status_message('')
				return
			sublime.status_message(self.success_message)
			return

		before = i % self.size
		after = (self.size - 1) - before

		sublime.status_message('%s [%s=%s]' % (self.message, ' ' * before, ' ' * after))

		if not after:
			self.addend = -1
		if not before:
			self.addend = 1
		i += self.addend

		sublime.set_timeout(lambda: self.run(i), 100)
