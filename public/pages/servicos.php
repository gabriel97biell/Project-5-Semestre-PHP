<?php
require_once '../../includes/config.php';
require_once '../../includes/header.php';
?>

<!-- Hero com imagem de fundo e os cards sobrepostos -->
<section class="hero-experiencias">
  <div class="conteudo-hero">
   

    <main class="skills" id="skills">
      <div class="skill-items">
        <!-- CARD 1 -->
        <div class="item">
          <h4>Nuvem para Empresa X</h4>
          <p>Soluções modernas com AWS que aumentaram a eficiência e segurança operacional.</p>
          <button class="btn-saiba-mais" onclick="mostrarDetalhes(this)">Saiba mais</button>
          <div class="detalhes">
            <p>Implementamos uma infraestrutura em nuvem altamente escalável baseada na AWS para a Empresa X. A solução permitiu a modernização completa dos seus sistemas internos, com foco em segurança, automação e desempenho. Adicionamos balanceamento de carga, automação de backups diários, criptografia de dados em repouso e em trânsito, e configuramos alarmes de monitoramento em tempo real com integração a sistemas de resposta automática. O projeto proporcionou uma redução de 40% no tempo de resposta das aplicações e uma economia significativa nos custos de infraestrutura.</p>
          </div>
        </div>

        <!-- CARD 2 -->
        <div class="item">
          <h4>Aplicativo Móvel para Startup Y</h4>
          <p>Aplicativo robusto e escalável com infraestrutura em nuvem, foco em usabilidade.</p>
          <button class="btn-saiba-mais" onclick="mostrarDetalhes(this)">Saiba mais</button>
          <div class="detalhes">
            <p>Criamos um aplicativo mobile responsivo e interativo para a Startup Y, com foco total na experiência do usuário. Desenvolvido com tecnologias modernas como React Native, o app conta com sistema de autenticação segura, notificacoes push em tempo real, integração com APIs externas e um painel administrativo na web para gestão de usuários e conteúdo. O design foi pensado para ser intuitivo, com uma experiência fluida tanto em Android quanto iOS. Em apenas 30 dias após o lançamento, o aplicativo conquistou mais de 10 mil downloads.</p>
          </div>
        </div>

        <!-- CARD 3 -->
        <div class="item">
          <h4>Gestão Sustentável</h4>
          <p>Redução de consumo e descarte ecologicamente correto de equipamentos.</p>
          <button class="btn-saiba-mais" onclick="mostrarDetalhes(this)">Saiba mais</button>
          <div class="detalhes">
            <p>Desenvolvemos um plano completo de sustentabilidade digital com foco na eficiência energética e na gestão adequada de resíduos eletrônicos. Isso incluiu auditorias técnicas para identificar consumo excessivo, substituição de equipamentos obsoletos, reconfiguração de servidores para operação sob demanda e parcerias com recicladoras certificadas para descarte correto. O programa resultou em 23% de economia na conta de energia e na obtenção de selos ambientais reconhecidos nacionalmente.</p>
          </div>
        </div>

        <!-- CARD 4 -->
        <div class="item">
          <h4>Suporte Remoto</h4>
          <p>Infraestrutura em nuvem para home office eficiente.</p>
          <button class="btn-saiba-mais" onclick="mostrarDetalhes(this)">Saiba mais</button>
          <div class="detalhes" style="display: none;">
            <p>Durante a pandemia, implementamos uma solução completa para trabalho remoto seguro e produtivo. Os colaboradores passaram a acessar sistemas internos via VPN criptografada, com autenticação de dois fatores, e suporte técnico em tempo real via chat e acesso remoto. Foram implantadas ferramentas colaborativas como Microsoft Teams e Google Workspace, com formação personalizada para todos os funcionários. Isso garantiu 100% de continuidade nas operações, mesmo com equipes descentralizadas, e melhoria na comunicação interna.</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</section>

<link rel="stylesheet" href="../../assets/styles/servicos.css">
<script>
  function mostrarDetalhes(botao) {
    const todosDetalhes = document.querySelectorAll(".detalhes");

    todosDetalhes.forEach(div => {
      if (div !== botao.nextElementSibling) {
        div.style.display = "none";
      }
    });

    const detalhes = botao.nextElementSibling;
    detalhes.style.display = (detalhes.style.display === "block") ? "none" : "block";
  }
</script>

<?php
require_once '../../includes/footer.php';?>