# -*- coding: utf-8 -*-
import sublime, sublime_plugin, sys
# import sublime_lib
import os, codecs, subprocess, string, json, threading, re, time,webbrowser
import base64,binascii

from .thread_progress import ThreadProgress

package_name = 'PhpNinJaManual'
packages_path = os.path.split(os.path.realpath(__file__))[0]
command_bin = packages_path + os.sep + 'tp5' + os.sep + 'public' + os.sep + 'index.php'

def open_tab(url):
	webbrowser.open_new_tab(url)

def PKGPATH():
	global package_name
	return os.path.join(sublime.packages_path(), package_name)

def fs_reader(path):
    return codecs.open(path, mode='r', encoding='utf8').read()

def fs_writer(path, raw):
    codecs.open(path, mode='w', encoding='utf8', errors='ignore').write(raw)


class php_execute(threading.Thread):
	def __init__(self, cmd, args, view, window):
		self.cmd = cmd
		self.args = args
		self.view = view
		self.window = window
		settings = sublime.load_settings("PhpBox.sublime-settings")
		self.php_bin = settings.get('php_path')
		global command_bin
		threading.Thread.__init__(self)

	def run(self):

		command_text = self.php_bin+' "' + command_bin + '" sublime/index/run/call/'+self.cmd+'/args/'+ base64.b64encode(json.dumps(self.args, sort_keys=True).encode('utf-8')).decode('utf-8')
		print(command_text)
		cloums = os.popen(command_text)
		data = cloums.read()
		print(data)
		self.parse_php_result(data)

	def parse_php_result(self, out):
		try:
			print(out)
			result_str_raw = out
			# out = out.decode("UTF-8")
		except UnicodeDecodeError as e:
			print(out)
		try:
			result_str = base64.b64decode(out)
		except (TypeError):
			print(out)
			pass
		except (binascii.Error):
			print(out)
			pass
		result = 0;
		try:
			result = json.loads(result_str.decode('utf-8'))
		except (ValueError):
			print('The return value for the php plugin is wrong JSON.',True)
			if len(result_str) > 0:
				try:
					sublime.error_message(u"PHP ERROR:\n{0}".format(result_str.decode('utf-8')))
				except(UnicodeDecodeError):
					sublime.error_message(u"PHP ERROR:\n{0}".format(result_str_raw))
			pass
		print(type(result))
		print(result)
		# -------------------------------------------------------------------
		#                 PHP 通信完成，开始处理结果
		# -------------------------------------------------------------------
		self.executeReceive(result)

	def executeReceive(self, result):
		print(result)
		if result['code'] == 200:
			if result['type'] == 'error_dialog':
				sublime.error_message(u"{0}".format(result['msg']))
			elif result['type'] == 'msg_dialog':
				sublime.message_dialog(u"{0}".format(result['msg']))
			elif result['type'] == 'ok_cancel_dialog':
				ret = sublime.ok_cancel_dialog(u"{0}".format(result['msg']), u"{0}".format(result['ok_title']))
				if ret is True:
					if result['ok_cmd'] != []:
						self.executeReceive(result['ok_cmd'])
			elif result['type'] == 'yes_no_cancel_dialog':
				ret = sublime.yes_no_cancel_dialog(u"{0}".format(result['msg']), u"{0}".format(result['yes_title']), u"{0}".format(result['no_title']))
				if ret == sublime.DIALOG_CANCEL:
					pass
				elif ret == sublime.DIALOG_YES:
					if result['yes_cmd'] != []:
						self.executeReceive(result['yes_cmd'])
				elif ret == sublime.DIALOG_NO:
					if result['no_cmd'] != []:
						self.executeReceive(result['no_cmd'])
			elif result['type'] == 'status_message':
				sublime.status_message(u"{0}".format(result['msg']));
			elif result['type'] == 'set_clipboard':
				ret = sublime.set_clipboard(u"{0}".format(result['str']))
			elif result['type'] == 'run_command':
				self.window.run_command(u"{0}".format(result['cmd']), result['args'])
			elif result['type'] == 'show_quick_panel':
				def on_quick_done(index):
					if result['on_done_cmd'] == []:
						pass
					else:
						# result.args 合并 index
						# print('index:')
						# print(index)
						# print(result['on_done_cmd'])
						if 'cmd_args' in result['on_done_cmd']['args']:
							result['on_done_cmd']['args']['cmd_args']['index'] = index
							result['on_done_cmd']['args']['inner'] = 1
						else:
							result['on_done_cmd']['args']['index'] = index
						# print(result['on_done_cmd']['args'])
						self.window.run_command(u"{0}".format(result['on_done_cmd']['cmd']), result['on_done_cmd']['args'])
				def on_quick_highlighted(index):
					if result['on_highlighted_cmd'] == []:
						pass
					else:
						# result.args 合并 index
						print(index)
						if 'cmd_args' in result['on_highlighted_cmd']['args']:
							result['on_highlighted_cmd']['args']['cmd_args']['index'] = index
							result['on_highlighted_cmd']['args']['inner'] = 1
						else:
							result['on_highlighted_cmd']['args']['index'] = index
						print(result['on_highlighted_cmd']['args'])
						self.window.run_command(u"{0}".format(result['on_highlighted_cmd']['cmd']), result['on_highlighted_cmd']['args'])
				ret = self.window.show_quick_panel(result['items'], on_quick_done, result['flag'], -1, on_quick_highlighted)
			elif result['type'] == 'show_input_panel':
				def on_input_done(str):
					if result['on_done_cmd'] == []:
						pass
					else:
						# result.args 合并 str
						if 'cmd_args' in result['on_done_cmd']['args']:
							result['on_done_cmd']['args']['inner'] = 1
							result['on_done_cmd']['args']['cmd_args']['str'] = str
						else:
							result['on_done_cmd']['args']['str'] = str
						self.window.run_command(u"{0}".format(result['on_done_cmd']['cmd']), result['on_done_cmd']['args'])
				def on_input_change(str):
					if result['on_change_cmd'] == []:
						pass
					else:
						# result['args'] 合并 str
						if 'cmd_args' in result['on_change_cmd']['args']:
							result['on_change_cmd']['args']['inner'] = 1
							result['on_change_cmd']['args']['cmd_args']['str'] = str
						else:
							result['on_change_cmd']['args']['str'] = str
						print(result['on_change_cmd']['args'])
						self.window.run_command(u"{0}".format(result['on_change_cmd']['cmd']), result['on_change_cmd']['args'])
				def on_input_cancel():
					if result['on_cancel_cmd'] == []:
						pass
					else:
						if 'cmd_args' in result['on_cancel_cmd']['args']:
							result['on_cancel_cmd']['args']['inner'] = 1
						self.window.run_command(u"{0}".format(result['on_cancel_cmd']['cmd']), result['on_cancel_cmd']['args'])
				ret = self.window.show_input_panel(u"{0}".format(result['caption']), u"{0}".format(result['initial_text']), on_input_done, on_input_change, on_input_cancel)
			elif result['type'] == 'show_popup_menu':
				def on_done(index):
					if result['on_done_cmd'] == []:
						pass
					else:
						# result['args'] 合并 str
						if 'cmd_args' in result['on_done_cmd']['args']:
							result['on_done_cmd']['args']['cmd_args']['index'] = index
							result['on_done_cmd']['args']['inner'] = 1
						else:
							result['on_done_cmd']['args']['index'] = index
						self.window.run_command(u"{0}".format(result['on_done_cmd']['cmd']), result['on_done_cmd']['args'])
				self.view.show_popup_menu(result['items'], on_done)
			elif result['type'] == 'show_popup':
				def on_navigate(url):
					if result['on_navigate_cmd'] == []:
						pass
					else:
						# result['args'] 合并 str
						if 'cmd_args' in result['on_navigate_cmd']['args']:
							result['on_navigate_cmd']['args']['cmd_args']['url'] = url
							result['on_navigate_cmd']['args']['inner'] = 1
						else:
							result['on_navigate_cmd']['args']['url'] = url
						print(json.dumps(result['on_navigate_cmd']))
						self.window.run_command(u"{0}".format(result['on_navigate_cmd']['cmd']), result['on_navigate_cmd']['args'])
				def on_hide():
					if result['on_hide_cmd'] == []:
						pass
					else:
						# result['args'] 合并 str
						if 'cmd_args' in result['on_hide_cmd']['args']:
							result['on_hide_cmd']['args']['inner'] = 1
						self.window.run_command(u"{0}".format(result['on_hide_cmd']['cmd']), result['on_hide_cmd']['args'])
				self.view.show_popup(result['content'], result['flags'], result['location'], result['max_width'], result['max_height'], on_navigate, on_hide)
			elif result['type'] == 'open_tab':
				open_tab(result['url'])
			elif result['type'] == 'run_command':
				cmd = u"{0}".format(result['cmd'])
				result['args']['inner'] = 1
				if result['from'] == 'window':
					self.window.run_command(cmd, result['args'])
				elif result['from'] == 'view':
					self.view.run_command(cmd, result['args'])
				elif result['from'] == 'applicant':
					sublime.run_command(cmd, result['args'])
		else:
			sublime.error_message(u"{0}".format(result['msg']))

def check_php(path):
	php_path = path
	check_php_path = os.popen(php_path + ' -v').read()
	# print(php_path);
	# print(check_php_path)
	pattern = re.compile(r'^PHP \d+.\d+');
	if pattern.match(check_php_path):
		check_php_path = True;
	else:
		check_php_path = False;
	if check_php_path == False:
		sublime.windows()[0].show_input_panel(u'Please input php bin path', '/usr/local/bin/php', self.done, None, None)
	return check_php_path

	def done(self,path):
		settings = sublime.load_settings("PhpBox.sublime-settings")
		settings.set('php_path', path)

class PhpBoxCommand(sublime_plugin.TextCommand):
	def refresh_curr(self, curr):
		obj = packages_path + os.sep + 'curr.json'
		fs_writer(obj, json.dumps(curr, sort_keys=True, indent=4, separators=(',', ': ')))
		# self.settings.get('curr')
	def get_sel(self):
		region = self.view.sel()[0]
		if region.begin() != region.end():
			return self.view.substr(region)
		else:
			return ''
	def run(self, edit, call, cmd_args, inner = 0):
		curr_win = sublime.active_window()
		# sublime.error_message(self.get_sel())
		curr_view = self.view
		curr = {
			"window":{
				"window":{
					"id":curr_win.id(),
					"num_groups":curr_win.num_groups(),
					"active_group":curr_win.active_group(),
					"is_menu_visible":curr_win.is_menu_visible(),
					"is_sidebar_visible":curr_win.is_sidebar_visible(),
					"get_tabs_visible":curr_win.get_tabs_visible(),
					"is_minimap_visible":curr_win.is_minimap_visible(),
					"is_status_bar_visible":curr_win.is_status_bar_visible(),
					"folders":curr_win.folders(),
					"project_file_name":curr_win.project_file_name(),
					"project_data":curr_win.project_data(),
					"active_panel":curr_win.active_panel(),
					"panels":curr_win.panels(),
				}
			},
			"view":{
				"id":curr_view.id(),
				"buffer_id":curr_view.buffer_id(),
				"is_primary":curr_view.is_primary(),
				"file_name":curr_view.file_name(),
				"name":curr_view.name(),
				"is_dirty":curr_view.is_dirty(),
				"is_read_only":curr_view.is_read_only(),
				"is_scratch":curr_view.is_scratch(),
				"size":curr_view.size(),
				"viewport_extent":curr_view.viewport_extent(),
				"layout_extent":curr_view.layout_extent(),
				"line_height":curr_view.line_height(),
				"em_width":curr_view.em_width(),
				"change_count":curr_view.change_count(),
				"encoding":curr_view.encoding(),
				"line_endings":curr_view.line_endings(),
				"is_popup_visible":curr_view.is_popup_visible(),
				"is_auto_complete_visible":curr_view.is_auto_complete_visible(),
				"style":curr_view.style(),
				"sel":self.get_sel()
			},
			"package_path":PKGPATH()
		}
		print('curr')
		# print(curr)
		if('inner' not in cmd_args):
			self.refresh_curr(curr)
		# return
		self.settings = sublime.load_settings("PhpBox.sublime-settings")

		if(check_php(self.settings.get('php_path'))) == False:
			return
		# print(command_bin)
		# print(call)
		# print(cmd_args)
		if call == '':
			thread = php_execute('app\\sublime\\command\\ListCmd', cmd_args, self.view, sublime.windows()[0])
		else:
			thread = php_execute(call, cmd_args, self.view, sublime.windows()[0])
		thread.start()
		ThreadProgress(thread, 'Is excuting', 'Finding Done')
