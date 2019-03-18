# -*- coding: utf-8 -*-

import sublime, sublime_plugin
import styled_popup
import codecs,os,subprocess,threading,json,webbrowser,base64
from .php_box import PhpBoxCommand
from subprocess import PIPE
from .thread_progress import ThreadProgress

packages_path = os.path.split(os.path.realpath(__file__))[0]

def fs_reader(path):
	return codecs.open(path, mode='r', encoding='utf8').read()

# def open_tab(url):
# 	webbrowser.open_new_tab(url)

class open_tab(PhpNinJaManualCommand, sublime_plugin.TextCommand):
	def run(self, url):
		webbrowser.open_new_tab(url)

class PhpNinJaManualCommand(sublime_plugin.TextCommand):
	def run(self, edit):
		return ''

class show_php_document(PhpNinJaManualCommand, sublime_plugin.TextCommand):
	def run(self, edit):
		region = self.view.sel()[0]
		if region.begin() != region.end():
			function = self.view.substr(region)
			thread = find_comment(function, self.view, sublime.windows()[0])
			thread.start()
			ThreadProgress(thread, 'Is excuting', 'Finding Done')
		else:
			sublime.status_message('must be a word')

class find_comment(threading.Thread):
	def __init__(self, word, view, window):
		self.word = word
		self.view = view
		self.window = window
		threading.Thread.__init__(self)

	def run(self):
		settings = sublime.load_settings('PhpNinJaManual.sublime-settings')
		print(settings.get('lang'))
		lang = settings.get('lang')
		return self.window.run_command('php_box', {
			'call':'app\\sublime\\command\\FindCommentCmd',
			'cmd_args':{
				'function':self.word,
				'lang':lang
			}
		})