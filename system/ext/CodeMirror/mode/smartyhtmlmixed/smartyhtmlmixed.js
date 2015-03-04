CodeMirror.defineMode("smartymixed", function(config, parserConfig) {
var htmlMode = CodeMirror.getMode(config, {name: "xml", htmlMode: true});
var smartyMode = CodeMirror.getMode(config, "smarty");
var jsMode = CodeMirror.getMode(config, "javascript");
var cssMode = CodeMirror.getMode(config, "css");



function html(stream, state) {
  var style = htmlMode.token(stream, state.htmlState);
  if (style == "tag" && stream.current() == ">" && state.htmlState.context) {
    if (/^script$/i.test(state.htmlState.context.tagName)) {
      state.token = javascript;
      state.localState = jsMode.startState(htmlMode.indent(state.htmlState, ""));
      state.mode = "javascript";
    }
    else if (/^style$/i.test(state.htmlState.context.tagName)) {
      state.token = css;
      state.localState = cssMode.startState(htmlMode.indent(state.htmlState, ""));
      state.mode = "css";
    }
  }

  return style;
}
function maybeBackup(stream, pat, style) {
  var cur = stream.current();
  var close = cur.search(pat);
  if (close > -1) stream.backUp(cur.length - close);
  return style;
}
function javascript(stream, state) {
  if (stream.match(/^<\/\s*script\s*>/i, false)) {
    state.token = html;
    state.localState = null;
    state.mode = "html";
    return html(stream, state);
  }
  return maybeBackup(stream, /<\/\s*script\s*>/,
                     jsMode.token(stream, state.localState));
}
function css(stream, state) {
  if (stream.match(/^<\/\s*style\s*>/i, false)) {
    state.token = html;
    state.localState = null;
    state.mode = "html";
    return html(stream, state);
  }
  return maybeBackup(stream, /<\/\s*style\s*>/,
                     cssMode.token(stream, state.localState));
}

function smarty(stream, state) {
   style =  smartyMode.token(stream, state.localState);
   if ( state.localState.tokenize == null )
        { // back to anything from smarty
        state.token = state.htmlState.tokens.pop();
        state.mode = state.htmlState.modes.pop();
        state.localState = state.htmlState.states.pop(); // state.htmlState;
        }
    return(style);
    }

return {

  startState: function() {
    var state = htmlMode.startState();
    state.modes = [];
    state.tokens = [];
    state.states = [];
    return {token: html, localState: null, mode: "html", htmlState: state};
  },

  copyState: function(state) {
    if (state.localState)
      var local = CodeMirror.copyState(
          ( state.token == css ) ? cssMode : (( state.token == javascript ) ? jsMode : smartyMode ),
          state.localState);
    return {token: state.token, localState: local, mode: state.mode,
            htmlState: CodeMirror.copyState(htmlMode, state.htmlState)};
  },

  token: function(stream, state) {

    if ( stream.match(/^{[^ ]{1}/,false) )
        { // leaving anything to smarty
        state.htmlState.states.push(state.localState);
        state.htmlState.tokens.push(state.token);
        state.htmlState.modes.push(state.mode);
        state.token = smarty;
            state.localState = smartyMode.startState();
            state.mode = "smarty";
        }

    return state.token(stream, state);
  },


  compareStates: function(a, b) {
    if (a.mode != b.mode) return false;
    if (a.localState) return CodeMirror.Pass;
    return htmlMode.compareStates(a.htmlState, b.htmlState);
  },

  electricChars: "/{}:"
}
}, "xml", "javascript", "css", "smarty");

CodeMirror.defineMIME("text/html", "smartymixed");
