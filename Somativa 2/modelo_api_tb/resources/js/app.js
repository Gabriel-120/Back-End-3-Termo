import './voice-api';

/**
 * Voice API - Funcionalidades de Text-to-Speech em Português
 * Utiliza Web Speech API nativa do navegador
 */

class VoiceAPI {
  constructor() {
    this.synth = window.speechSynthesis;
    this.isSpeaking = false;
    this.lang = 'pt-BR';
  }

  /**
   * Fala um texto em português
   * @param {string} text - Texto a ser falado
   * @param {number} rate - Velocidade da fala (0.5 a 2)
   */
  speak(text, rate = 1) {
    if (!this.synth) {
      console.warn('Web Speech API não suportada neste navegador');
      return;
    }

    // Cancelar fala anterior se estiver em andamento
    this.synth.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = this.lang;
    utterance.rate = rate;
    utterance.pitch = 1;
    utterance.volume = 1;

    utterance.onstart = () => {
      this.isSpeaking = true;
    };

    utterance.onend = () => {
      this.isSpeaking = false;
    };

    utterance.onerror = (event) => {
      console.error('Erro ao falar:', event.error);
      this.isSpeaking = false;
    };

    this.synth.speak(utterance);
  }

  /**
   * Para a fala atual
   */
  stop() {
    if (this.synth) {
      this.synth.cancel();
      this.isSpeaking = false;
    }
  }

  /**
   * Fala as instruções do jogo
   */
  speakGameStart() {
    this.speak('Quem é esse Pokémon?', 0.9);
  }

  /**
   * Fala que o jogador acertou
   * @param {string} pokemonName - Nome do Pokémon
   */
  speakGameCorrect(pokemonName) {
    this.speak(`Parabéns! Você acertou! Era o ${pokemonName}!`, 1);
  }

  /**
   * Fala que o jogador errou
   * @param {string} pokemonName - Nome do Pokémon correto
   */
  speakGameWrong(pokemonName) {
    this.speak(`Errado! Era o ${pokemonName}. Tente novamente!`, 1);
  }

  /**
   * Fala as informações do Pokémon na Pokédex
   * @param {object} pokemonData - Dados do Pokémon
   */
  speakPokemonInfo(pokemonData) {
    let text = '';

    // 1. ID do Pokémon
    if (pokemonData.id) {
      text += `Número do Pokémon: ${pokemonData.id}. `;
    }

    // 2. Nome
    if (pokemonData.name) {
      text += `Nome: ${pokemonData.name}. `;
    }

    // 3. Tipo(s)
    if (pokemonData.types && pokemonData.types.length > 0) {
      const typeNames = pokemonData.types.map((t) => {
        const type = t.type?.name || t;
        return type.charAt(0).toUpperCase() + type.slice(1);
      });
      text += `Tipo: ${typeNames.join(' e ')}. `;
    }

    // 4. Descrição do Pokémon
    if (pokemonData.description) {
      text += `Descrição: ${pokemonData.description}. `;
    }

    // 5. Linha evolutiva
    if (pokemonData.evolutions && pokemonData.evolutions.length > 0) {
      const evolutionNames = pokemonData.evolutions.map((e) => e.name || e);
      const stagesCount = evolutionNames.length;

      if (stagesCount === 1) {
        text += `${pokemonData.name} não possui evolução. `;
      } else if (stagesCount === 2) {
        text += `${pokemonData.name} tem 2 estágios de evolução: ${evolutionNames[0]} e ${evolutionNames[1]}. `;
      } else if (stagesCount === 3) {
        text += `${pokemonData.name} tem 3 estágios de evolução, começando com ${evolutionNames[0]}, evoluindo para ${evolutionNames[1]}, e finalizando sua linha evolutiva como ${evolutionNames[2]}. `;
      } else {
        text += `Linha evolutiva: ${evolutionNames.join(', ')}. `;
      }
    }

    // 6. Formas alternativas
    if (pokemonData.variants && pokemonData.variants.length > 1) {
      const otherVariants = pokemonData.variants.filter((v) => !v.is_current);
      if (otherVariants.length > 0) {
        const variantForms = otherVariants.map((v) => v.form || v.name);
        text += `Formas alternativas: ${variantForms.join(', ')}. `;
      }
    }

    // 7. Localizações onde pode ser encontrado
    if (pokemonData.locations && pokemonData.locations.length > 0) {
      if (pokemonData.locations.length === 1) {
        text += `Pode ser encontrado em: ${pokemonData.locations[0]}. `;
      } else {
        text += `Pode ser encontrado em: ${pokemonData.locations.join(', ')}. `;
      }
    }

    // 8. Stats
    if (pokemonData.stats && pokemonData.stats.length > 0) {
      text += `Stats: `;
      pokemonData.stats.forEach((stat, index) => {
        const statNameRaw = stat.stat?.name || stat.name || '';
        let statName = statNameRaw
          .replace(/-/g, ' ')
          .replace('hp', 'vida')
          .replace('attack', 'ataque')
          .replace('defense', 'defesa')
          .replace('sp atk', 'ataque especial')
          .replace('sp def', 'defesa especial')
          .replace('speed', 'velocidade');

        const statValue = stat.base_stat || stat.value || 0;
        text += `${statName}: ${statValue}`;

        if (index < pokemonData.stats.length - 1) {
          text += ', ';
        }
      });
      text += '. ';
    }

    this.speak(text, 0.85);
  }
}

// Criar instância global
window.voiceAPI = new VoiceAPI();
