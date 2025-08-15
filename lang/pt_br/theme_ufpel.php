<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Arquivo de idioma para theme_ufpel - Português Brasil (Completo)
 *
 * @package    theme_ufpel
 * @copyright  2025 Universidade Federal de Pelotas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General strings
$string['pluginname'] = 'UFPel';
$string['choosereadme'] = 'UFPel é um tema moderno baseado no Boost, personalizado para a Universidade Federal de Pelotas, totalmente compatível com o Moodle 5.x e Bootstrap 5.';

// Settings page strings
$string['configtitle'] = 'Configurações do tema UFPel';
$string['generalsettings'] = 'Configurações gerais';
$string['advancedsettings'] = 'Configurações avançadas';
$string['features'] = 'Funcionalidades';
$string['performance'] = 'Desempenho';
$string['accessibility'] = 'Acessibilidade';
$string['default'] = 'Padrão';

// Settings headings
$string['colorsheading'] = 'Configurações de cores';
$string['colorsheading_desc'] = 'Configure o esquema de cores para o seu tema. Essas cores serão aplicadas em todo o site.';
$string['logoheading'] = 'Logo e identidade visual';
$string['logoheading_desc'] = 'Envie o logotipo da sua instituição e configure os elementos de identidade visual.';
$string['loginheading'] = 'Configurações da página de login';
$string['loginheading_desc'] = 'Personalize a aparência da página de login.';
$string['cssheading'] = 'CSS/SCSS personalizado';
$string['cssheading_desc'] = 'Adicione código CSS ou SCSS personalizado para personalizar ainda mais a aparência do tema.';

// Color settings
$string['primarycolor'] = 'Cor primária';
$string['primarycolor_desc'] = 'A cor primária do tema. Será usada em elementos principais como cabeçalho e botões.';
$string['secondarycolor'] = 'Cor secundária';
$string['secondarycolor_desc'] = 'A cor secundária do tema. Usada para links e elementos secundários.';
$string['accentcolor'] = 'Cor de destaque';
$string['accentcolor_desc'] = 'A cor de destaque usada para realces e elementos especiais em todo o site.';
$string['backgroundcolor'] = 'Cor de fundo';
$string['backgroundcolor_desc'] = 'A cor de fundo principal para as páginas do site.';
$string['highlightcolor'] = 'Cor de realce';
$string['highlightcolor_desc'] = 'A cor usada para destacar elementos importantes e acentos.';
$string['contenttextcolor'] = 'Cor do texto do conteúdo';
$string['contenttextcolor_desc'] = 'A cor do texto geral do conteúdo em todo o site.';
$string['highlighttextcolor'] = 'Cor do texto destacado';
$string['highlighttextcolor_desc'] = 'A cor do texto que aparece em fundos coloridos primários.';

// Feature settings
$string['showcourseimage'] = 'Mostrar imagem do curso';
$string['showcourseimage_desc'] = 'Exibir a imagem do curso no cabeçalho das páginas do curso.';
$string['showteachers'] = 'Mostrar professores';
$string['showteachers_desc'] = 'Exibir nomes dos professores no cabeçalho das páginas do curso.';
$string['courseheaderoverlay'] = 'Sobreposição do cabeçalho do curso';
$string['courseheaderoverlay_desc'] = 'Adicionar uma sobreposição escura ao cabeçalho do curso para melhorar a legibilidade do texto.';
$string['footercontent'] = 'Conteúdo do rodapé';
$string['footercontent_desc'] = 'Conteúdo HTML personalizado para exibir no rodapé do site.';

// Logo and images
$string['logo'] = 'Logo';
$string['logo_desc'] = 'Envie o logotipo da sua instituição. Isso substituirá o nome do site na barra de navegação. Altura recomendada: 40px.';
$string['footerlogo'] = 'Logo do rodapé';
$string['footerlogo_desc'] = 'Um logotipo separado para a área do rodapé. Se não definido, o logotipo principal será usado.';
$string['loginbackgroundimage'] = 'Imagem de fundo da página de login';
$string['loginbackgroundimage_desc'] = 'Uma imagem que será exibida como plano de fundo da página de login. Tamanho recomendado: 1920x1080 ou maior.';
$string['favicon'] = 'Favicon';
$string['favicon_desc'] = 'Envie um favicon personalizado. Deve ser um arquivo .ico, .png ou .svg.';

// Configurações do logo
$string['logowidth'] = 'Largura do logo';
$string['logowidth_desc'] = 'Defina uma largura personalizada para o logo em pixels. Deixe vazio para dimensionamento automático.';
$string['showsitenamewithlogo'] = 'Mostrar nome do site com o logo';
$string['showsitenamewithlogo_desc'] = 'Exibir o nome do site ao lado do logo na barra de navegação.';
$string['compactlogo'] = 'Logo compacto';
$string['compactlogo_desc'] = 'Uma versão menor do logo para dispositivos móveis. Se não configurado, o logo principal será usado.';
$string['logodisplaymode'] = 'Modo de exibição do logo';
$string['logodisplaymode_desc'] = 'Escolha como o logo deve ser exibido em diferentes tamanhos de tela.';
$string['logodisplaymode_responsive'] = 'Responsivo (adapta-se ao tamanho da tela)';
$string['logodisplaymode_fixed'] = 'Tamanho fixo';
$string['logodisplaymode_compact'] = 'Sempre compacto';

// Custom CSS/SCSS
$string['customcss'] = 'CSS personalizado';
$string['customcss_desc'] = 'Quaisquer regras CSS que você adicionar a esta área de texto serão refletidas em todas as páginas, facilitando a personalização deste tema.';
$string['rawscss'] = 'SCSS bruto';
$string['rawscss_desc'] = 'Use este campo para fornecer código SCSS que será injetado no final da folha de estilo.';
$string['rawscsspre'] = 'SCSS inicial bruto';
$string['rawscsspre_desc'] = 'Neste campo você pode fornecer código SCSS de inicialização, ele será injetado antes de todo o resto. Na maioria das vezes você usará esta configuração para definir variáveis.';

// Preset settings
$string['preset'] = 'Predefinição do tema';
$string['preset_desc'] = 'Escolha uma predefinição para alterar amplamente a aparência do tema.';
$string['preset_default'] = 'Padrão';
$string['preset_dark'] = 'Modo escuro';
$string['presetfiles'] = 'Arquivos de predefinição de tema adicionais';
$string['presetfiles_desc'] = 'Arquivos de predefinição podem ser usados para alterar drasticamente a aparência do tema.';

// Font settings
$string['customfonts'] = 'URL de fontes personalizadas';
$string['customfonts_desc'] = 'Insira a URL para importar fontes personalizadas (por exemplo, Google Fonts). Use a declaração @import completa.';

// Footer strings - REQUIRED for footer template
$string['footerdescription'] = 'Sistema de gestão de aprendizagem da Universidade Federal de Pelotas';
$string['quicklinks'] = 'Links rápidos';
$string['support'] = 'Suporte';
$string['policies'] = 'Políticas';
$string['contactus'] = 'Fale conosco';
$string['mobileapp'] = 'Aplicativo móvel';
$string['downloadapp'] = 'Baixe o aplicativo Moodle';
$string['allrightsreserved'] = 'Todos os direitos reservados';
$string['poweredby'] = 'Desenvolvido com';
$string['theme'] = 'Tema';

// Navigation strings - REQUIRED for navigation
$string['home'] = 'Início';
$string['courses'] = 'Cursos';
$string['myhome'] = 'Painel';
$string['calendar'] = 'Calendário';
$string['help'] = 'Ajuda';
$string['documentation'] = 'Documentação';
$string['login'] = 'Entrar';
$string['logout'] = 'Sair';
$string['privacy'] = 'Privacidade';
$string['privacypolicy'] = 'Política de privacidade';
$string['dataprivacy'] = 'Privacidade de dados';

// Login page strings
$string['username'] = 'Nome de usuário';
$string['password'] = 'Senha';
$string['rememberusername'] = 'Lembrar nome de usuário';
$string['loginsite'] = 'Entrar no site';
$string['startsignup'] = 'Criar nova conta';
$string['forgotten'] = 'Esqueceu seu nome de usuário ou senha?';
$string['firsttime'] = 'É sua primeira vez aqui?';
$string['newaccount'] = 'Criar uma nova conta';
$string['loginguest'] = 'Entrar como visitante';
$string['someallowguest'] = 'Alguns cursos podem permitir acesso de visitantes';
$string['forgotaccount'] = 'Perdeu a senha?';

// Course page strings - CORRECTED
$string['teacher'] = 'Professor';
$string['teachers'] = 'Professores';
$string['enrolledusers'] = '{$a} usuários inscritos';
$string['startdate'] = 'Data de início';
$string['enddate'] = 'Data de término';
$string['coursecompleted'] = 'Parabéns! Você concluiu este curso.';
$string['congratulations'] = 'Parabéns!';
$string['progress'] = 'Progresso';
$string['complete'] = 'completo';
$string['courseheader'] = 'Cabeçalho do curso';
$string['breadcrumb'] = 'Navegação estrutural';
$string['courseprogress'] = 'Progresso do curso';
$string['coursecompletion'] = 'Conclusão do curso';

// User interface strings
$string['darkmodeon'] = 'Modo escuro ativado';
$string['darkmodeoff'] = 'Modo escuro desativado';
$string['totop'] = 'Voltar ao topo';
$string['skipmain'] = 'Pular para o conteúdo principal';
$string['skipnav'] = 'Pular navegação';
$string['skipnavigation'] = 'Pular navegação';
$string['skipmainmenu'] = 'Pular menu principal';
$string['skipmaincontent'] = 'Pular para o conteúdo principal';
$string['skipsettingsmenu'] = 'Pular menu de configurações';
$string['skipfooter'] = 'Pular para o rodapé';
$string['themepreferences'] = 'Preferências do tema';

// Privacy strings
$string['privacy:metadata'] = 'O tema UFPel não armazena nenhum dado pessoal.';
$string['privacy:metadata:preference:darkmode'] = 'Preferência do usuário para o modo escuro.';
$string['privacy:metadata:preference:compactview'] = 'Preferência do usuário para visualização compacta.';
$string['privacy:metadata:preference:draweropen'] = 'Preferência do usuário para o estado da gaveta de navegação.';

// Region strings
$string['region-side-pre'] = 'Esquerda';
$string['region-side-post'] = 'Direita';

// Accessibility strings
$string['skipto'] = 'Pular para {$a}';
$string['accessibilitymenu'] = 'Menu de acessibilidade';
$string['increasefontsize'] = 'Aumentar tamanho da fonte';
$string['decreasefontsize'] = 'Diminuir tamanho da fonte';
$string['resetfontsize'] = 'Redefinir tamanho da fonte';
$string['highcontrast'] = 'Alto contraste';
$string['normalcontrast'] = 'Contraste normal';

// Notification strings
$string['loading'] = 'Carregando...';
$string['error'] = 'Erro';
$string['success'] = 'Sucesso';
$string['warning'] = 'Aviso';
$string['info'] = 'Informação';
$string['close'] = 'Fechar';
$string['expand'] = 'Expandir';
$string['collapse'] = 'Recolher';
$string['menu'] = 'Menu';
$string['search'] = 'Buscar';
$string['filter'] = 'Filtrar';
$string['sort'] = 'Ordenar';
$string['settings'] = 'Configurações';
$string['notifications'] = 'Notificações';

// Additional feature strings
$string['showcourseprogressinheader'] = 'Mostrar progresso no cabeçalho';
$string['showcourseprogressinheader_desc'] = 'Exibir a barra de progresso do curso no cabeçalho quando o rastreamento de conclusão estiver ativado.';
$string['showcoursesummary'] = 'Mostrar resumo do curso';
$string['showcoursesummary_desc'] = 'Exibir o resumo do curso no cabeçalho da página do curso.';
$string['enablelazyloading'] = 'Ativar carregamento preguiçoso';
$string['enablelazyloading_desc'] = 'Carregar imagens e iframes apenas quando necessário para melhorar o desempenho.';
$string['enablecssoptimization'] = 'Otimizar CSS';
$string['enablecssoptimization_desc'] = 'Ativar otimização e minificação de CSS para melhor desempenho.';
$string['enableresourcehints'] = 'Ativar dicas de recursos';
$string['enableresourcehints_desc'] = 'Usar preload e prefetch para melhorar o carregamento de recursos.';
$string['enableanimations'] = 'Ativar animações';
$string['enableanimations_desc'] = 'Ativar animações e transições suaves. Desative para melhor desempenho em dispositivos mais lentos.';
$string['enableaccessibilitytools'] = 'Ferramentas de acessibilidade';
$string['enableaccessibilitytools_desc'] = 'Ativar ferramentas adicionais de acessibilidade como ajuste de contraste e controles de tamanho de fonte.';
$string['enabledarkmode'] = 'Ativar modo escuro';
$string['enabledarkmode_desc'] = 'Permitir que os usuários alternem para o modo escuro.';
$string['enablecompactview'] = 'Ativar visualização compacta';
$string['enablecompactview_desc'] = 'Permitir que os usuários alternem para uma visualização mais compacta.';

// Social media strings
$string['social_facebook'] = 'URL do Facebook';
$string['social_facebook_desc'] = 'URL da página do Facebook da instituição';
$string['social_twitter'] = 'URL do Twitter/X';
$string['social_twitter_desc'] = 'URL da página do Twitter/X da instituição';
$string['social_linkedin'] = 'URL do LinkedIn';
$string['social_linkedin_desc'] = 'URL da página do LinkedIn da instituição';
$string['social_youtube'] = 'URL do YouTube';
$string['social_youtube_desc'] = 'URL do canal do YouTube da instituição';
$string['social_instagram'] = 'URL do Instagram';
$string['social_instagram_desc'] = 'URL da página do Instagram da instituição';

// Additional strings for completeness
$string['dashboard'] = 'Painel';
$string['sitehome'] = 'Página inicial do site';
$string['participants'] = 'Participantes';
$string['reports'] = 'Relatórios';
$string['badges'] = 'Emblemas';
$string['competencies'] = 'Competências';
$string['grades'] = 'Notas';
$string['messages'] = 'Mensagens';
$string['preferences'] = 'Preferências';
$string['timeline'] = 'Linha do tempo';
$string['mycourses'] = 'Meus cursos';
$string['allcourses'] = 'Todos os cursos';
$string['coursecategories'] = 'Categorias de cursos';
$string['coursecategory'] = 'Categoria do curso';
$string['recentactivity'] = 'Atividade recente';
$string['nocoursesyet'] = 'Nenhum curso disponível ainda';
$string['viewallcourses'] = 'Ver todos os cursos';
$string['nocourses'] = 'Nenhum curso';
$string['enrollmentkey'] = 'Chave de inscrição';
$string['courseaccess'] = 'Acesso ao curso';
$string['userprofile'] = 'Perfil do usuário';
$string['editprofile'] = 'Editar perfil';
$string['termsofuse'] = 'Termos de uso';
$string['datasecurity'] = 'Segurança de dados';
$string['copyright'] = 'Direitos autorais';
$string['siteadmin'] = 'Administração do site';
$string['sitemenu'] = 'Menu do site';
$string['navigationmenu'] = 'Menu de navegação';
$string['usermenu'] = 'Menu do usuário';
$string['languagemenu'] = 'Menu de idiomas';

// Time related
$string['today'] = 'Hoje';
$string['yesterday'] = 'Ontem';
$string['tomorrow'] = 'Amanhã';
$string['lastweek'] = 'Semana passada';
$string['nextweek'] = 'Próxima semana';
$string['lastmonth'] = 'Mês passado';
$string['nextmonth'] = 'Próximo mês';

// Actions
$string['edit'] = 'Editar';
$string['delete'] = 'Excluir';
$string['save'] = 'Salvar';
$string['cancel'] = 'Cancelar';
$string['submit'] = 'Enviar';
$string['view'] = 'Visualizar';
$string['download'] = 'Baixar';
$string['upload'] = 'Enviar';
$string['select'] = 'Selecionar';
$string['open'] = 'Abrir';
$string['more'] = 'Mais';
$string['less'] = 'Menos';
$string['browsecourses'] = 'Procurar cursos';
$string['popularlinks'] = 'Links populares';
$string['quickaccess'] = 'Acesso rápido';
$string['needhelp'] = 'Precisa de ajuda?';
$string['contactsupport'] = 'Contatar suporte';
$string['welcomeback'] = 'Bem-vindo de volta!';
$string['logintitle'] = 'Entrar no Moodle UFPel';
$string['logindescription'] = 'Por favor, insira suas credenciais para acessar a plataforma de aprendizagem.';

// Status messages
$string['completed'] = 'Concluído';
$string['incomplete'] = 'Incompleto';
$string['inprogress'] = 'Em andamento';
$string['notstarted'] = 'Não iniciado';
$string['processing'] = 'Processando...';

// Development and debugging
$string['version'] = 'Versão';
$string['author'] = 'Autor';
$string['license'] = 'Licença';
$string['website'] = 'Site';
$string['repository'] = 'Repositório';
$string['issuetracker'] = 'Rastreador de problemas';
$string['documentation_link'] = 'Link da documentação';

// Error messages
$string['error:missinglogo'] = 'Logo não encontrado';
$string['error:invalidcolor'] = 'Código de cor inválido';
$string['error:fileuploadfailed'] = 'Falha no envio do arquivo';

// Help strings
$string['help:primarycolor'] = 'Esta cor será aplicada aos principais elementos da interface';
$string['help:darkmode'] = 'O modo escuro reduz o cansaço visual em ambientes com pouca luz';
$string['help:lazyloading'] = 'O carregamento preguiçoso melhora significativamente o desempenho em páginas com muitas imagens';

// Administrative strings
$string['themesettings'] = 'Configurações do tema UFPel';
$string['resetsettings'] = 'Redefinir configurações';
$string['resetsettings_desc'] = 'Redefinir todas as configurações do tema para os valores padrão';
$string['settingssaved'] = 'Configurações salvas com sucesso';
$string['settingsreset'] = 'Configurações redefinidas para os valores padrão';